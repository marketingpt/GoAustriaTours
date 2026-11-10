<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DestinationSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\QuizSeeder;
use Database\Seeders\TourTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([TourTypeSeeder::class, DestinationSeeder::class, PackageSeeder::class, QuizSeeder::class, ContentSeeder::class]);
    }

    public function test_sitemap_lists_public_pages_only(): void
    {
        $hidden = Package::first();
        $hidden->update(['is_published' => false]);
        $noindex = Package::skip(1)->first();
        $noindex->update(['noindex' => true]);
        $visible = Package::skip(2)->first();

        $res = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringContainsString('application/xml', $res->headers->get('Content-Type'));
        $xml = $res->getContent();

        $this->assertStringContainsString('<urlset', $xml);
        $this->assertStringContainsString('<loc>'.route('home').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('packages.index').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('packages.show', $visible->slug).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('destinations.show', 'vienna').'</loc>', $xml);
        $this->assertStringNotContainsString(route('packages.show', $hidden->slug), $xml);
        $this->assertStringNotContainsString(route('packages.show', $noindex->slug), $xml);
        $this->assertStringNotContainsString('thank-you', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
        $this->assertNotFalse(simplexml_load_string($xml), 'sitemap must be valid XML');
    }

    public function test_robots_blocks_admin_and_thank_you(): void
    {
        $body = $this->get('/robots.txt')->assertOk()->getContent();

        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Disallow: /thank-you', $body);
        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $body);
    }

    public function test_every_public_page_has_one_h1_title_description_and_canonical(): void
    {
        $slug = Package::first()->slug;
        foreach (['/', '/packages', "/packages/{$slug}", '/destinations', '/destinations/vienna', '/about', '/contact', '/terms-and-conditions', '/privacy-policy'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html), "one H1 on {$url}");
            $this->assertMatchesRegularExpression('/<title>[^<]{10,}<\/title>/', $html, "title on {$url}");
            $this->assertMatchesRegularExpression('/<meta name="description" content="[^"]{20,}"/', $html, "description on {$url}");
            $this->assertMatchesRegularExpression('/<link rel="canonical" href="[^"]+"/', $html, "canonical on {$url}");
        }
    }

    public function test_home_has_faq_structured_data_and_404_page_works(): void
    {
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('"@type":"FAQPage"', $html);
        $this->assertStringContainsString('"@type":"TravelAgency"', $html);

        $this->get('/no-such-page')->assertNotFound()->assertSee('We can');
        $this->get('/packages/does-not-exist')->assertNotFound();
    }

    public function test_gtm_snippet_only_when_configured(): void
    {
        $this->get('/')->assertDontSee('googletagmanager.com', false);

        Setting::put('gtm_id', 'GTM-ABC1234');
        $this->get('/')->assertSee('GTM-ABC1234', false);
    }
}
