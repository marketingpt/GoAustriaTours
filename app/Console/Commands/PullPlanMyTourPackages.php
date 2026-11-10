<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\Setting;
use App\Models\TourType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PullPlanMyTourPackages extends Command
{
    protected $signature = 'pmt:sync-packages {--company-id=3} {--dry-run} {--force-deletes}';

    protected $description = 'Sync packages from the public PlanMyTour feed into the local landing page database.';

    public function handle(): int
    {
        $companyId = (int) $this->option('company-id');
        $url = rtrim((string) env('PMT_PUBLIC_FEED_URL', 'https://api.planmytour.app/api/v2/public/packages'), '/').'/'.$companyId;
        $etag = (string) Setting::get('pmt_feed_etag', '');

        try {
            $response = Http::withHeaders($etag !== '' ? ['If-None-Match' => $etag] : [])
                ->acceptJson()
                ->timeout(30)
                ->get($url);
        } catch (\Throwable $e) {
            $message = 'PMT sync failed to reach feed: '.$e->getMessage();
            Log::error($message);
            $this->error($message);

            return self::FAILURE;
        }

        if ($response->status() === 304) {
            $this->info('PMT feed unchanged (ETag 304).');
            Log::info('PMT feed unchanged for company '.$companyId.' (304).');

            return self::SUCCESS;
        }

        if ($response->status() !== 200) {
            $message = 'PMT feed returned HTTP '.$response->status().' for company '.$companyId.'.';
            Log::error($message);
            $this->error($message);

            return self::FAILURE;
        }

        $body = $response->body();
        if (! is_string($body) || trim($body) === '') {
            $message = 'PMT feed returned an empty response for company '.$companyId.'.';
            Log::error($message);
            $this->error($message);

            return self::FAILURE;
        }

        $payload = json_decode($body, true);
        if (! is_array($payload) || ! ($payload['response'] ?? false) || ! is_array($payload['packages'] ?? null)) {
            $message = 'PMT feed payload was invalid or not marked as successful for company '.$companyId.'.';
            Log::error($message);
            $this->error($message);

            return self::FAILURE;
        }

        $etagHeader = (string) $response->header('ETag');
        if ($etagHeader !== '') {
            Setting::put('pmt_feed_etag', $etagHeader);
        }

        $payloadPackages = $payload['packages'];
        $incomingIds = collect($payloadPackages)->pluck('id')->filter()->map(fn ($id) => (int) $id)->values()->all();
        $incomingSet = array_fill_keys($incomingIds, true);
        $existingPmtIds = Package::query()->whereNotNull('source_id')->pluck('source_id')->map(fn ($id) => (int) $id)->all();

        $removalCount = count(array_diff($existingPmtIds, $incomingIds));
        if ($removalCount > 0 && ! $this->option('force-deletes')) {
            $threshold = max(1, (int) ceil(count($existingPmtIds) * 0.5));
            if ($removalCount >= $threshold) {
                $message = 'Skipping PMT deletes: '.$removalCount.' local PMT packages would be removed (>50% of existing PMT-sourced rows).';
                Log::warning($message);
                $this->warn($message);

                return self::FAILURE;
            }
        }

        $this->syncTypeLookup($payload['types']['tour_types'] ?? []);

        $added = 0;
        $updated = 0;
        $removed = 0;

        if ($this->option('dry-run')) {
            $this->info('Fetched '.$payload['package_count'].' PMT packages for company '.$companyId.'.');
            $this->line('Dry run only: '.count($incomingIds).' ids would be upserted and '.count(array_diff($existingPmtIds, $incomingIds)).' would be removed.');
            Log::info('Dry-run PMT sync for company '.$companyId.': fetched='.count($incomingIds).' added=0 updated=0 removed=0');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($payloadPackages, &$added, &$updated, &$removed, $incomingSet) {
                foreach ($payloadPackages as $packageRow) {
                    $pmtId = (int) ($packageRow['id'] ?? 0);
                    if ($pmtId <= 0) {
                        continue;
                    }

                    $values = $this->normalizePackage($packageRow, $payload['company'] ?? [], $payload['types']['tour_types'] ?? []);
                    $row = Package::query()->where('source_id', $pmtId)->first();

                    if ($row) {
                        $row->fill($values);
                        $row->save();
                        if ($row->wasChanged()) {
                            $updated++;
                        }
                    } else {
                        Package::query()->create($values);
                        $added++;
                    }
                }

                $removalIds = Package::query()->whereNotNull('source_id')->whereNotIn('source_id', array_keys($incomingSet))->pluck('source_id')->map(fn ($id) => (int) $id)->all();
                $removed = count($removalIds);
                if ($removed > 0) {
                    Package::query()->whereNotNull('source_id')->whereNotIn('source_id', array_keys($incomingSet))->delete();
                }
            });
        } catch (\Throwable $e) {
            $message = 'PMT sync transaction failed: '.$e->getMessage();
            Log::error($message, ['company_id' => $companyId, 'url' => $url]);
            $this->error($message);

            return self::FAILURE;
        }

        Log::info('PMT sync complete for company '.$companyId.': fetched='.count($payloadPackages).' added='.$added.' updated='.$updated.' removed='.$removed.'');
        $this->info('PMT sync complete: fetched '.count($payloadPackages).' | added '.$added.' | updated '.$updated.' | removed '.$removed.'');

        return self::SUCCESS;
    }

    protected function syncTypeLookup(array $types): void
    {
        foreach ($types as $type) {
            $name = trim((string) ($type['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            TourType::query()->updateOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }

    protected function normalizePackage(array $package, array $company, array $types = []): array
    {
        $tourTypeName = null;

        if (! empty($package['tour_type_ids'])) {
            $pmTypeId = (int) $package['tour_type_ids'][0];
            $tourTypeName = $this->typeNameFromLookup($pmTypeId, $types);
        }

        $tourType = $tourTypeName ? TourType::query()->firstOrCreate([
            'slug' => Str::slug($tourTypeName),
        ], ['name' => $tourTypeName, 'is_active' => true]) : null;

        $title = trim((string) ($package['name'] ?? 'Untitled package'));
        $sourceId = (int) ($package['id'] ?? 0);
        $image = is_string($package['hero_image'] ?? null) ? $package['hero_image'] : ($package['photo'] ?? null);
        $summary = trim((string) ($package['short_description'] ?? ''));
        $description = is_string($package['description'] ?? null) ? (string) $package['description'] : $summary;

        return [
            'title' => $title,
            'slug' => $this->uniqueSlug($title, $sourceId),
            'source_id' => $sourceId,
            'tour_type_id' => $tourType?->id,
            'destination_id' => null,
            'nights' => (int) (($package['duration'] ?? $package['overnights'] ?? 7) ?: 7),
            'price_from' => (float) ($package['price'] ?? 0),
            'currency' => strtoupper((string) (($package['currency']['code'] ?? 'USD') ?: 'USD')),
            'price_note' => ! empty($package['price_strikeout']) ? 'From '.($package['currency']['symbol'] ?? '').$package['price_strikeout'] : null,
            'summary' => $summary,
            'description' => $description,
            'highlights' => $this->asArray($package['highlights'] ?? null),
            'itinerary' => $this->normalizeDays($package['days'] ?? []),
            'inclusions' => $this->asArray($package['package_includes'] ?? null),
            'exclusions' => [],
            'faqs' => [],
            'media_id' => null,
            'seo_title' => $title,
            'meta_description' => $summary,
            'noindex' => false,
            'is_published' => true,
            'is_featured' => false,
            'is_sample' => true,
            'sort_order' => 0,
        ];
    }

    protected function typeNameFromLookup(int $pmTypeId, array $types): ?string
    {
        foreach ($types as $type) {
            if ((int) ($type['id'] ?? 0) === $pmTypeId) {
                return trim((string) ($type['name'] ?? ''));
            }
        }

        return null;
    }

    protected function normalizeDays(array $days): array
    {
        return collect($days)->map(function ($day) {
            $label = trim((string) (($day['name'] ?? '') ?: ('Day '.($day['day_no'] ?? ''))));
            $body = trim((string) ($day['description'] ?? ''));

            return ['title' => $label, 'body' => $body];
        })->filter(fn ($item) => $item['title'] !== '' || $item['body'] !== '')->values()->all();
    }

    protected function asArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value), fn ($v) => $v !== ''));
        }

        $string = trim((string) $value);

        if ($string === '') {
            return [];
        }

        return [$string];
    }

    protected function uniqueSlug(string $title, int $sourceId): string
    {
        $base = Str::slug($title) ?: 'package-'.$sourceId;
        $slug = $base;
        $counter = 1;

        while (Package::query()->where('slug', $slug)->where('source_id', '!=', $sourceId)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
