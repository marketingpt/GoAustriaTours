<?php

/**
 * Editable Home page text. key => [label, default, type] where type is text | textarea | media.
 * Admin > Pages > Home renders these fields; the view reads them with $home->text('key', default).
 * Keep claims honest: do not add statistics, awards or review counts unless they are real.
 */
return [
    'Hero' => [
        'hero_eyebrow' => ['Eyebrow', 'Austria · Tailor-made journeys', 'text'],
        'hero_title' => ['Headline (line 1)', 'Explore Austria,', 'text'],
        'hero_title_accent' => ['Headline (italic line 2)', 'your way.', 'text'],
        'hero_sub' => ['Sub-heading', 'Self-drive, chauffeur and coach journeys shaped around your pace.', 'textarea'],
        'hero_media_id' => ['Hero image', '', 'media'],
        'badge_1_title' => ['Trust badge 1 title', 'Free, no-obligation quote', 'text'],
        'badge_1_text' => ['Trust badge 1 text', 'Tell us your plans, we do the planning.', 'text'],
        'badge_2_title' => ['Trust badge 2 title', 'Tailor-made itineraries', 'text'],
        'badge_2_text' => ['Trust badge 2 text', 'Never one-size-fits-all.', 'text'],
        'badge_3_title' => ['Trust badge 3 title', 'Dedicated trip specialist', 'text'],
        'badge_3_text' => ['Trust badge 3 text', 'One point of contact from quote to return.', 'text'],
    ],
    'Why us' => [
        'why_eyebrow' => ['Eyebrow', 'Why book with us', 'text'],
        'why_title' => ['Title', 'Travel with confidence, arrive with anticipation', 'text'],
        'why_media_id' => ['Image', '', 'media'],
        'why_1_title' => ['Point 1 title', 'Tailor-made, never off-the-shelf', 'text'],
        'why_1_text' => ['Point 1 text', 'Every itinerary is built around your dates, pace and interests.', 'textarea'],
        'why_2_title' => ['Point 2 title', 'Austria know-how', 'text'],
        'why_2_text' => ['Point 2 text', 'Routes, stays and experiences chosen for how they feel on the ground, not just how they look in a brochure.', 'textarea'],
        'why_3_title' => ['Point 3 title', 'One person from start to finish', 'text'],
        'why_3_text' => ['Point 3 text', 'A single point of contact to answer questions before and during your trip.', 'textarea'],
        'why_4_title' => ['Point 4 title', 'Your choice of travel style', 'text'],
        'why_4_text' => ['Point 4 text', 'Drive yourself, travel with a private chauffeur, or join a coach journey.', 'textarea'],
    ],
    'Tour types' => [
        'types_eyebrow' => ['Eyebrow', 'Travel styles', 'text'],
        'types_title' => ['Title', 'Choose how you want to see Austria', 'text'],
        'types_text' => ['Intro', 'Three ways to experience the same extraordinary country. Not sure which suits you? Ask us.', 'textarea'],
    ],
    'Packages' => [
        'packages_eyebrow' => ['Eyebrow', 'Featured journeys', 'text'],
        'packages_title' => ['Title', 'Journeys to inspire your plans', 'text'],
        'packages_text' => ['Intro', 'Every journey can be adjusted. Treat these as a starting point.', 'textarea'],
    ],
    'How it works' => [
        'how_eyebrow' => ['Eyebrow', 'How it works', 'text'],
        'how_title' => ['Title', 'From first idea to boarding pass', 'text'],
        'how_1_title' => ['Step 1 title', 'Tell us your plans', 'text'],
        'how_1_text' => ['Step 1 text', 'Answer a few quick questions or send us a message. It takes less than a minute.', 'textarea'],
        'how_2_title' => ['Step 2 title', 'Receive your tailored quote', 'text'],
        'how_2_text' => ['Step 2 text', 'A specialist prepares an itinerary and a clear price built around you.', 'textarea'],
        'how_3_title' => ['Step 3 title', 'Travel with confidence', 'text'],
        'how_3_text' => ['Step 3 text', 'Once you approve the plan, we handle the arrangements so you can look forward to the trip.', 'textarea'],
    ],
    'Band (full-width image)' => [
        'band_media_id' => ['Image', '', 'media'],
        'band_title' => ['Title', 'Picture yourself by an Alpine lake.', 'text'],
        'band_text' => ['Text', 'Tell us what you imagine and we will shape the journey around it.', 'text'],
    ],
    'Testimonials, FAQ and final call to action' => [
        'testimonials_eyebrow' => ['Testimonials eyebrow', 'Guest stories', 'text'],
        'testimonials_title' => ['Testimonials title', 'What our guests say', 'text'],
        'faq_eyebrow' => ['FAQ eyebrow', 'Good to know', 'text'],
        'faq_title' => ['FAQ title', 'Frequently asked questions', 'text'],
        'cta_title' => ['Final call-to-action title', 'Ready to plan your Austrian escape?', 'text'],
        'cta_text' => ['Final call-to-action text', 'Request a free quote and a specialist will be in touch.', 'textarea'],
    ],
];
