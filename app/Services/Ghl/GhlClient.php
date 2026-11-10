<?php

namespace App\Services\Ghl;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/** Thin GoHighLevel API v2 client (Private Integration Token). The token never appears in logs or errors. */
class GhlClient
{
    public const CACHE_KEY = 'ghl.custom_field_ids';

    public function enabled(): bool
    {
        return ghl_enabled();
    }

    public function locationId(): string
    {
        return (string) config('services.ghl.location_id');
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(config('services.ghl.base_url'))
            ->withToken((string) config('services.ghl.token'))
            ->withHeaders(['Version' => config('services.ghl.version')])
            ->acceptJson()->asJson()->timeout(20);
    }

    private function check(Response $r, string $what): array
    {
        if ($r->failed()) {
            $msg = "GHL {$what} failed (HTTP {$r->status()}): ".mb_substr((string) $r->body(), 0, 300);
            throw new GhlException($this->scrub($msg));
        }

        return $r->json() ?? [];
    }

    private function scrub(string $text): string
    {
        $token = (string) config('services.ghl.token');

        return $token !== '' ? str_replace($token, '[redacted]', $text) : $text;
    }

    public function scrubMessage(\Throwable $e): string
    {
        return mb_substr($this->scrub($e->getMessage()), 0, 500);
    }

    /** @return array<int,array> raw custom field definitions for the contact model */
    public function listFields(): array
    {
        $json = $this->check($this->http()->get("/locations/{$this->locationId()}/customFields", ['model' => 'contact']), 'list custom fields');

        return $json['customFields'] ?? [];
    }

    public function createField(string $name, string $dataType): array
    {
        $json = $this->check($this->http()->post("/locations/{$this->locationId()}/customFields", [
            'name' => $name, 'dataType' => $dataType, 'model' => 'contact',
        ]), "create field '{$name}'");

        return $json['customField'] ?? $json;
    }

    /** name => id, cached for an hour. */
    public function fieldMap(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, 3600, fn () => collect($this->listFields())->pluck('id', 'name')->all());
    }

    public function findContactByEmail(string $email): ?array
    {
        $r = $this->http()->get('/contacts/search/duplicate', ['locationId' => $this->locationId(), 'email' => $email]);
        if ($r->status() === 404) {
            return null;
        }

        return $this->check($r, 'find contact')['contact'] ?? null;
    }

    /** @return array{id:string,new:bool} */
    public function upsert(array $payload): array
    {
        $json = $this->check($this->http()->post('/contacts/upsert', $payload), 'upsert contact');
        $id = $json['contact']['id'] ?? null;
        if (! $id) {
            throw new GhlException('GHL upsert returned no contact id.');
        }

        return ['id' => $id, 'new' => (bool) ($json['new'] ?? false)];
    }

    public function deleteContact(string $id): void
    {
        $this->check($this->http()->delete("/contacts/{$id}"), 'delete contact');
    }
}
