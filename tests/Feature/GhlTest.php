<?php

namespace Tests\Feature;

use App\Jobs\SyncEnquiryToGhl;
use App\Models\Enquiry;
use App\Services\Ghl\GhlClient;
use App\Services\Ghl\GhlFields;
use App\Services\Ghl\GhlPayloadBuilder;
use Database\Seeders\QuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GhlTest extends TestCase
{
    use RefreshDatabase;

    private function enquiry(string $form, array $first = [], array $last = []): Enquiry
    {
        return Enquiry::create([
            'form_name' => $form, 'first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'jane@example.com',
            'country_code' => '1', 'phone_national' => '2125550123', 'phone_e164' => '+12125550123',
            'preferred_date' => '2027-05', 'second_quote' => 'yes', 'special_request' => 'Hi', 'package_name' => $form === 'Package Quote' ? 'Sample Trip' : null,
            'quiz_answers' => $form === 'Hero Quiz' ? ['time_of_year' => ['label' => 'Time of Year', 'question' => 'Q', 'answer' => 'Autumn']] : null,
            'consent_given' => true, 'consent_text' => 'text', 'terms_version' => '2026-01', 'consent_at' => now(),
            'source' => $first['utm_source'] ?? 'direct',
            'tracking_first' => $first ?: null, 'tracking_last' => $last ?: null,
            'ghl_status' => 'pending',
        ]);
    }

    /** field name => id, using the name itself as the id so assertions are readable */
    private function ids(): array
    {
        $this->seed(QuizSeeder::class);

        return collect(array_keys(GhlFields::all()))->mapWithKeys(fn ($n) => [$n => $n])->all();
    }

    private function values(array $payload): array
    {
        return collect($payload['customFields'])->pluck('field_value', 'id')->all();
    }

    public function test_payload_includes_utm_and_tracking_fields_for_every_form_type(): void
    {
        $ids = $this->ids();
        $first = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'austria', 'utm_term' => 'austria tours', 'utm_content' => 'ad1', 'gclid' => 'G1', 'gbraid' => 'GB1', 'wbraid' => 'WB1', 'referrer' => 'https://google.com/', 'landing_page' => 'https://x.test/?gclid=G1'];
        $last = ['utm_source' => 'newsletter', 'utm_medium' => 'email'];

        foreach (['Hero Quiz', 'Header Modal', 'Package Quote', 'Contact Page'] as $form) {
            $payload = app(GhlPayloadBuilder::class)->build($this->enquiry($form, $first, $last), $ids);
            $v = $this->values($payload);

            foreach ($first as $key => $value) {
                $this->assertSame($value, $v[GhlFields::tracking($key, 'first')] ?? null, "$form: {$key}_first");
            }
            $this->assertSame('newsletter', $v['utm_source_last'], "$form: utm_source_last");
            $this->assertSame('email', $v['utm_medium_last'], "$form: utm_medium_last");
            $this->assertSame($form, $v[GhlFields::FORM_NAME], "$form: form name");
            $this->assertSame('Yes', $v[GhlFields::CONSENT_GIVEN]);
            $this->assertSame('2026-01', $v[GhlFields::CONSENT_VERSION]);
            $this->assertSame('+12125550123', $payload['phone']);
            $this->assertContains($form === 'Hero Quiz' ? 'form: Hero Quiz' : "form: $form", $payload['tags']);
        }
    }

    public function test_quiz_answers_are_sent_individually_and_combined(): void
    {
        $payload = app(GhlPayloadBuilder::class)->build($this->enquiry('Hero Quiz', ['utm_source' => 'google']), $this->ids());
        $v = $this->values($payload);

        $this->assertSame('Autumn', $v['Quiz - Time of Year']);
        $this->assertStringContainsString('Time of Year: Autumn', $v[GhlFields::QUIZ_ALL]);
    }

    public function test_returning_contact_keeps_first_touch_but_updates_last_touch(): void
    {
        $ids = $this->ids();
        $enquiry = $this->enquiry('Contact Page', ['utm_source' => 'second-visit-first'], ['utm_source' => 'second-visit-last']);
        $existing = [$ids['utm_source_first'] => 'original-google']; // already on the contact in GHL

        $v = $this->values(app(GhlPayloadBuilder::class)->build($enquiry, $ids, $existing));

        $this->assertArrayNotHasKey('utm_source_first', $v, 'existing first-touch value must not be overwritten');
        $this->assertSame('second-visit-last', $v['utm_source_last']);
    }

    public function test_sync_is_off_and_makes_no_http_calls_when_credentials_are_blank(): void
    {
        Http::fake();
        $this->assertFalse(ghl_enabled());
        $this->assertSame('', (string) config('services.ghl.token'));

        $enquiry = $this->enquiry('Contact Page');
        SyncEnquiryToGhl::dispatchSync($enquiry->id);

        Http::assertNothingSent();
        $this->assertSame('disabled', $enquiry->fresh()->ghl_status);
    }

    public function test_successful_sync_records_contact_id(): void
    {
        config(['services.ghl.token' => 'pit-SECRET-TOKEN', 'services.ghl.location_id' => 'LOC123']);
        $this->seed(QuizSeeder::class);
        Http::fake([
            '*/customFields*' => Http::response(['customFields' => [['id' => 'f1', 'name' => 'Form Name']]]),
            '*/contacts/search/duplicate*' => Http::response([], 404),
            '*/contacts/upsert' => Http::response(['contact' => ['id' => 'C-1'], 'new' => true]),
        ]);

        $ok = $this->enquiry('Contact Page');
        SyncEnquiryToGhl::dispatchSync($ok->id);
        $this->assertSame('synced', $ok->fresh()->ghl_status);
        $this->assertSame('C-1', $ok->fresh()->ghl_contact_id);
        Http::assertSent(fn ($r) => $r->hasHeader('Authorization', 'Bearer pit-SECRET-TOKEN') && $r->hasHeader('Version'));

    }

    public function test_failed_sync_never_leaks_the_token(): void
    {
        config(['services.ghl.token' => 'pit-SECRET-TOKEN', 'services.ghl.location_id' => 'LOC123']);
        $this->seed(QuizSeeder::class);
        Http::fake(['*' => Http::response('bad token pit-SECRET-TOKEN', 401)]);
        Cache::forget(GhlClient::CACHE_KEY);
        $bad = $this->enquiry('Contact Page');
        SyncEnquiryToGhl::dispatchSync($bad->id);

        $bad->refresh();
        $this->assertSame('failed', $bad->ghl_status);
        $this->assertStringNotContainsString('pit-SECRET-TOKEN', (string) $bad->ghl_error);
    }
}
