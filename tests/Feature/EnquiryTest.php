<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Package;
use App\Support\Consent;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DestinationSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\QuizSeeder;
use Database\Seeders\TourTypeSeeder;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([TourTypeSeeder::class, DestinationSeeder::class, PackageSeeder::class, QuizSeeder::class, ContentSeeder::class]);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'form_name' => 'Contact Page',
            'first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'Jane@Example.com',
            'country' => 'US', 'phone' => '(212) 555-0123',
            'preferred_date' => '2027-05', 'second_quote' => 'yes', 'special_request' => 'Window seat please',
            'consent' => '1',
            'g-recaptcha-response' => 'token-from-widget',
        ], $override);
    }

    /** A cookie value as the browser would send it back: encrypted with Laravel's cookie prefix. */
    private function cookie(string $name, string $value): string
    {
        $enc = app('encrypter');

        return $enc->encrypt(CookieValuePrefix::create($name, $enc->getKey()).$value, false);
    }

    private function recaptcha(bool $ok = true): void
    {
        Http::fake(['www.google.com/recaptcha/*' => Http::response(['success' => $ok])]);
    }

    public function test_enquiry_fails_without_consent(): void
    {
        $this->recaptcha();
        $data = $this->payload();
        unset($data['consent']);

        $this->postJson('/enquiry', $data)->assertStatus(422)->assertJsonValidationErrors('consent');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_with_unticked_consent_value(): void
    {
        $this->recaptcha();

        $this->postJson('/enquiry', $this->payload(['consent' => '0']))->assertStatus(422)->assertJsonValidationErrors('consent');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_without_recaptcha_token(): void
    {
        $data = $this->payload();
        unset($data['g-recaptcha-response']);

        $this->postJson('/enquiry', $data)->assertStatus(422)->assertJsonValidationErrors('g-recaptcha-response');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_when_google_rejects_the_token(): void
    {
        $this->recaptcha(false);

        $this->postJson('/enquiry', $this->payload())->assertStatus(422)->assertJsonValidationErrors('g-recaptcha-response');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_closed_when_google_is_unreachable(): void
    {
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $this->postJson('/enquiry', $this->payload())->assertStatus(422)->assertJsonValidationErrors('g-recaptcha-response');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->recaptcha();

        $this->postJson('/enquiry', $this->payload(['company_website' => 'http://spam.example']))->assertStatus(422);
        $this->assertSame(0, Enquiry::count());
    }

    public function test_valid_enquiry_is_saved_with_consent_evidence_and_tracking(): void
    {
        $this->recaptcha();
        config(['site.defaults.legal_name' => 'GoAustriaTours']);

        $quiz = ['time_of_year' => 'Autumn', 'travel_style' => 'Driving yourself', 'group_size' => '2 people', 'nights' => '8 nights', 'bogus_key' => 'ignored'];
        $first = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'austria', 'gclid' => 'G123', 'landing_page' => 'https://goaustriatours.test/?gclid=G123'];
        $last = ['utm_source' => 'newsletter', 'utm_medium' => 'email'];

        $response = $this
            ->withCredentials()
            ->withCookies(['gat_first' => json_encode($first), 'gat_last' => json_encode($last)])
            ->withHeaders(['User-Agent' => 'TestBrowser/1.0', 'Referer' => 'https://goaustriatours.test/packages/x'])
            ->postJson('/enquiry', $this->payload(['form_name' => 'Hero Quiz', 'quiz' => $quiz]));

        $response->assertOk()->assertJson(['ok' => true, 'redirect' => route('thank-you')]);

        $e = Enquiry::firstOrFail();
        $this->assertSame('jane@example.com', $e->email);
        $this->assertSame('+12125550123', $e->phone_e164);
        $this->assertTrue($e->consent_given);
        $this->assertSame(Consent::text('GoAustriaTours'), $e->consent_text);
        $this->assertStringStartsWith('I agree to the Terms and Conditions and Privacy Policy provided by GoAustriaTours.', $e->consent_text);
        $this->assertStringEndsWith('Consent is not a condition of purchase.', $e->consent_text);
        $this->assertNotEmpty($e->terms_version);
        $this->assertNotNull($e->consent_at);
        $this->assertSame('127.0.0.1', $e->ip_address);
        $this->assertSame('TestBrowser/1.0', $e->user_agent);
        $this->assertSame('https://goaustriatours.test/packages/x', $e->page_url);
        $this->assertSame('Hero Quiz', $e->form_name);
        $this->assertSame('Autumn', $e->quiz_answers['time_of_year']['answer']);
        $this->assertArrayNotHasKey('bogus_key', $e->quiz_answers);
        $this->assertSame('Autumn', $e->time_of_year);
        $this->assertSame('google', $e->source);
        $this->assertSame('G123', $e->tracking_first['gclid']);
        $this->assertSame('newsletter', $e->tracking_last['utm_source']);
        $this->assertSame('disabled', $e->ghl_status); // GHL is blank in phpunit.xml
    }

    public function test_package_enquiry_stores_package_id_and_name(): void
    {
        $this->recaptcha();
        $package = Package::firstOrFail();

        $this->postJson('/enquiry', $this->payload(['form_name' => 'Package Quote', 'package_id' => $package->id]))->assertOk();

        $e = Enquiry::firstOrFail();
        $this->assertSame($package->id, $e->package_id);
        $this->assertSame($package->title, $e->package_name);
    }

    public function test_non_ajax_submission_redirects_to_thank_you(): void
    {
        $this->recaptcha();

        $this->post('/enquiry', $this->payload())->assertRedirect(route('thank-you'));
        $this->get('/thank-you')->assertOk()->assertSee('noindex', false)->assertSee('generate_lead', false);
        $this->get('/thank-you')->assertOk()->assertDontSee('generate_lead', false); // conversion fires once only
    }

    public function test_enquiry_form_is_rate_limited_per_ip(): void
    {
        $this->recaptcha(false);
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/enquiry', $this->payload())->assertStatus(422);
        }
        $this->postJson('/enquiry', $this->payload())->assertStatus(429);
    }

    public function test_unticked_consent_checkbox_is_rendered_on_forms(): void
    {
        $html = $this->get('/contact')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<input type="checkbox" id="contact-form-consent" name="consent" value="1" required class="[^"]*"\s/', $html);
        $this->assertStringNotContainsString('checked', substr($html, strpos($html, 'contact-form-consent'), 200));
        $this->assertStringContainsString('Consent is not a condition of purchase.', $html);
    }
}
