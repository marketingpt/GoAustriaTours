<?php

namespace App\Jobs;

use App\Models\Enquiry;
use App\Services\Ghl\GhlClient;
use App\Services\Ghl\GhlPayloadBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/** Upserts the enquirer as a GoHighLevel contact. Never blocks or fails the enquiry itself. */
class SyncEnquiryToGhl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 5;

    public function __construct(public int $enquiryId) {}

    public function backoff(): array
    {
        return [30, 120, 600, 1800];
    }

    public function handle(GhlClient $client, GhlPayloadBuilder $builder): void
    {
        $enquiry = Enquiry::find($this->enquiryId);
        if (! $enquiry) {
            return;
        }

        if (! $client->enabled()) {
            $enquiry->update(['ghl_status' => 'disabled']);

            return;
        }

        try {
            $fieldIds = $client->fieldMap();
            $existing = $client->findContactByEmail($enquiry->email);
            $existingValues = collect($existing['customFields'] ?? [])->mapWithKeys(fn ($f) => [($f['id'] ?? '') => $f['value'] ?? ($f['field_value'] ?? null)])->all();

            $result = $client->upsert($builder->build($enquiry, $fieldIds, $existingValues));

            $enquiry->update([
                'ghl_status' => 'synced', 'ghl_contact_id' => $result['id'],
                'ghl_error' => null, 'ghl_synced_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $message = $client->scrubMessage($e);
            Log::warning("GHL sync failed for enquiry #{$this->enquiryId}: {$message}");
            $enquiry->update(['ghl_status' => 'failed', 'ghl_error' => $message]);

            // On a real queue, rethrow so Laravel retries with backoff. With the sync driver, swallow it.
            if (config('queue.default') !== 'sync') {
                throw $e;
            }
        }
    }
}
