<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/** Starter page copy, FAQs and PLACEHOLDER testimonials. Replace in Admin > Pages before launch. */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $banner = '<p><strong>STARTER TEMPLATE, NOT LEGAL ADVICE.</strong> Have this text reviewed by a qualified lawyer and complete every [CONFIRM] item before launch. Remove this paragraph when done.</p>';

        $pages = [
            'home' => ['Home', null, ['hero_media_id' => MediaSeeder::id('hallstatt-lake'), 'why_media_id' => MediaSeeder::id('hikers'), 'band_media_id' => MediaSeeder::id('alpine-peaks')],
                'Austria Tours: Self Drive, Chauffeur & Coach | GoAustriaTours',
                'Tailor-made Austria tours for US travellers: self-drive, chauffeur-driven and coach journeys planned by specialists. Request your free quote.'],
            'about' => ['About Us', '<p>{company} designs tailor-made journeys through Austria, from imperial Vienna and Baroque Salzburg to the lakes and peaks of the Alps.</p><p>[CONFIRM: company story, team, years in business, accreditations. Do not publish figures that cannot be verified.]</p><h2>How we work</h2><p>You tell us when you want to travel, how you like to explore and what matters most. A specialist then prepares an itinerary and a clear quote built around you.</p>', [],
                'About GoAustriaTours', 'Learn about GoAustriaTours and how we plan tailor-made self-drive, chauffeur-driven and coach tours of Austria.'],
            'contact' => ['Contact Us', null, ['intro' => 'Tell us about your trip and a specialist will get back to you. You can also call us or send an email.'],
                'Contact GoAustriaTours | Request a Quote', 'Request a free quote for your Austria trip or ask us a question. We will get back to you.'],
            'terms' => ['Terms and Conditions', $banner.'<h2>1. About these terms</h2><p>These terms apply to your use of this website and to enquiries made through it. {company} ("we", "us") provides travel planning services. [CONFIRM: registered company details and address.]</p><h2>2. Enquiries and quotes</h2><p>Submitting an enquiry is not a booking. Quotes are subject to availability and may change until confirmed in writing. [CONFIRM: quote validity period.]</p><h2>3. Bookings, payments and cancellations</h2><p>[CONFIRM: deposit, final payment, cancellation and amendment terms.]</p><h2>4. Travel insurance and documents</h2><p>You are responsible for holding valid travel documents and appropriate travel insurance. [CONFIRM: insurance requirements.]</p><h2>5. SMS messages</h2><p>If you provide your phone number and agree, {company} may send SMS messages about your enquiry, booking updates, itinerary updates, reminders and customer support. Message and data rates may apply. Reply STOP to unsubscribe or HELP for assistance. Consent is not a condition of purchase.</p><h2>6. Liability</h2><p>[CONFIRM: limitation of liability and supplier terms, reviewed by a lawyer.]</p><h2>7. Governing law</h2><p>[CONFIRM: governing law and jurisdiction.]</p><h2>8. Contact</h2><p>Questions about these terms: see the contact details in the website footer.</p>', [],
                'Terms and Conditions', 'Terms and conditions for using this website and making enquiries.'],
            'privacy' => ['Privacy Policy', $banner.'<h2>1. Who we are</h2><p>{company} is responsible for the personal information collected through this website. [CONFIRM: registered details and a privacy contact.]</p><h2>2. What we collect</h2><p>Name, email address, phone number, trip preferences and any message you send. We also record the consent you give, the date and time, your IP address, browser details and the page you were on, as evidence of that consent. We use cookies and similar technologies to remember which advertising campaign brought you to us (for up to 30 days) and, where enabled, for analytics and advertising measurement.</p><h2>3. How we use it</h2><p>To respond to your enquiry, prepare quotes, contact you by phone, email and (with your consent) SMS, manage customer relationships in our CRM, improve this website and measure advertising.</p><h2>4. Sharing</h2><p>We use service providers such as website hosting, email delivery, spam protection (Google reCAPTCHA) and customer relationship management software (GoHighLevel). [CONFIRM: full list of processors and any international transfers.]</p><h2>5. SMS</h2><p>If you opt in, you may receive SMS messages from {company}. Message and data rates may apply. Reply STOP to unsubscribe or HELP for assistance. Mobile information is not shared with third parties for their marketing purposes. [CONFIRM with counsel.]</p><h2>6. Your rights</h2><p>Depending on where you live you may have rights to access, correct or delete your information. [CONFIRM: how to make a request and retention periods.]</p><h2>7. Contact</h2><p>See the contact details in the website footer.</p>', [],
                'Privacy Policy', 'How we collect, use and protect your personal information.'],
        ];

        foreach ($pages as $slug => [$title, $body, $content, $seo, $meta]) {
            Page::firstOrCreate(['slug' => $slug], [
                'title' => $title, 'body' => $body, 'content' => $content ?: null, 'seo_title' => $seo, 'meta_description' => $meta,
            ]);
        }

        if (! Faq::exists()) {
            $faqs = [
                ['How does getting a quote work?', 'Answer a few quick questions or send us a message. A specialist will review your dates and preferences and come back to you with a tailored itinerary and price. There is no obligation.'],
                ['What is the difference between self-drive, chauffeur-driven and coach tours?', 'On a self-drive tour you collect a car and follow a planned route at your own pace. A chauffeur-driven tour adds a private driver, so you can simply enjoy the scenery. A coach tour moves between highlights with a small group and a guide.'],
                ['Can I change one of the sample itineraries?', 'Yes. The journeys on this site are starting points. Tell us what you would like to add, remove or slow down.'],
                ['When is the best time to visit Austria?', 'It depends what you want. Summer suits lakes and high-mountain roads, autumn brings colour to the vineyards, winter suits city breaks and snow scenes, and spring is quieter. We can advise based on your plans.'],
                ['How far ahead should I plan?', '[CONFIRM: recommended booking lead times.] In general, the earlier you ask, the more choice you have for accommodation, especially in peak season.'],
                ['What does the price include?', '[CONFIRM: standard inclusions and exclusions.] Every quote lists exactly what is and is not included.'],
                ['Do I need a visa or special documents?', 'Entry requirements depend on your passport and may change. We will point you to the official sources to check before you travel.'],
                ['How do I pay?', '[CONFIRM: payment schedule and accepted methods.]'],
            ];
            foreach ($faqs as $i => [$q, $a]) {
                Faq::create(['question' => $q, 'answer' => $a, 'sort_order' => $i + 1]);
            }
        }

        $sampleTestimonials = [
            ['name' => 'Alex R.', 'location' => 'Seattle, WA', 'quote' => 'The planning process felt clear from the start. We could shape the itinerary around the places and pace that mattered to us.'],
            ['name' => 'Jordan T.', 'location' => 'Portland, OR', 'quote' => 'Having different travel styles to choose from made it easier to picture a trip that would suit our group.'],
            ['name' => 'Casey M.', 'location' => 'Chicago, IL', 'quote' => 'The details were easy to discuss, and the proposed journey gave us a helpful starting point for planning Austria.'],
        ];
        $placeholders = Testimonial::query()->where('is_placeholder', true)->orderBy('sort_order')->get();

        if ($placeholders->isEmpty() && ! Testimonial::exists()) {
            foreach ($sampleTestimonials as $index => $testimonial) {
                Testimonial::create([
                    'name' => $testimonial['name'], 'location' => $testimonial['location'], 'quote' => $testimonial['quote'],
                    'is_placeholder' => true, 'sort_order' => $index + 1,
                ]);
            }
        } else {
            foreach ($placeholders->take(count($sampleTestimonials)) as $index => $placeholder) {
                $placeholder->update([
                    'name' => $sampleTestimonials[$index]['name'], 'location' => $sampleTestimonials[$index]['location'],
                    'quote' => $sampleTestimonials[$index]['quote'],
                ]);
            }
        }
    }
}
