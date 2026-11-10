<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->longText('value')->nullable();
            $t->timestamps();
        });

        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->string('base_path');           // e.g. media/2026/abc123  (files: -480.webp, -960.webp, -1600.webp)
            $t->string('original_name')->nullable();
            $t->string('alt');
            $t->unsignedInteger('width');
            $t->unsignedInteger('height');
            $t->json('sizes');                 // [480, 960, 1600]
            $t->string('credit')->nullable();
            $t->timestamps();
        });

        Schema::create('tour_types', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('tagline')->nullable();
            $t->text('description')->nullable();
            $t->string('icon')->nullable();
            $t->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('destinations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('tagline')->nullable();
            $t->text('intro')->nullable();
            $t->longText('body')->nullable();
            $t->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->string('seo_title')->nullable();
            $t->string('meta_description')->nullable();
            $t->boolean('noindex')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->foreignId('tour_type_id')->nullable()->constrained('tour_types')->nullOnDelete();
            $t->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $t->unsignedSmallInteger('nights')->default(7);
            $t->decimal('price_from', 10, 2)->nullable();
            $t->string('currency', 3)->default('USD');
            $t->string('price_note')->nullable();
            $t->text('summary')->nullable();
            $t->longText('description')->nullable();
            $t->json('highlights')->nullable();
            $t->json('itinerary')->nullable();   // [{title, body}]
            $t->json('inclusions')->nullable();
            $t->json('exclusions')->nullable();
            $t->json('faqs')->nullable();        // [{question, answer}]
            $t->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->string('seo_title')->nullable();
            $t->string('meta_description')->nullable();
            $t->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->boolean('noindex')->default(false);
            $t->boolean('is_published')->default(true);
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_sample')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('package_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();
            $t->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();      // home, about, contact, terms, privacy
            $t->string('title');
            $t->longText('body')->nullable();  // rich text for about/terms/privacy
            $t->json('content')->nullable();   // flat key => text for structured pages (home, contact)
            $t->string('seo_title')->nullable();
            $t->string('meta_description')->nullable();
            $t->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->boolean('noindex')->default(false);
            $t->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('location')->nullable();
            $t->text('quote');
            $t->boolean('is_placeholder')->default(false);
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->string('question');
            $t->text('answer');
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('quiz_steps', function (Blueprint $t) {
            $t->id();
            $t->string('field_key')->unique();   // answer key, e.g. time_of_year
            $t->string('label');                 // short label (also CRM field name), e.g. "Time of Year"
            $t->string('question');
            $t->string('type')->default('tiles'); // tiles | select
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_step_id')->constrained()->cascadeOnDelete();
            $t->string('label');
            $t->string('icon')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $t) {
            $t->id();
            $t->string('form_name');
            $t->string('first_name');
            $t->string('last_name');
            $t->string('email');
            $t->string('country_code', 8);          // dial code digits, e.g. 1
            $t->string('phone_national', 30);
            $t->string('phone_e164', 20);
            $t->string('preferred_date')->nullable();
            $t->string('second_quote', 20)->nullable();
            $t->text('special_request')->nullable();
            $t->string('time_of_year', 60)->nullable();
            $t->string('group_size', 60)->nullable();
            $t->string('nights', 60)->nullable();
            $t->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $t->string('package_name')->nullable();
            $t->string('tour_type')->nullable();
            $t->json('quiz_answers')->nullable();

            // consent / compliance evidence
            $t->boolean('consent_given');
            $t->text('consent_text');
            $t->string('terms_version', 40);
            $t->timestamp('consent_at');
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->text('page_url')->nullable();

            // tracking (first and last touch)
            $t->string('source')->nullable();        // first-touch utm_source or "direct"
            $t->json('tracking_first')->nullable();
            $t->json('tracking_last')->nullable();

            // workflow
            $t->string('status', 20)->default('new');
            $t->text('notes')->nullable();

            // GoHighLevel sync
            $t->string('ghl_status', 20)->default('pending'); // pending|synced|failed|disabled
            $t->string('ghl_contact_id')->nullable();
            $t->text('ghl_error')->nullable();
            $t->timestamp('ghl_synced_at')->nullable();

            $t->timestamps();
            $t->index('status');
            $t->index('created_at');
            $t->index('email');
        });
    }

    public function down(): void
    {
        foreach (['enquiries', 'quiz_options', 'quiz_steps', 'faqs', 'testimonials', 'pages', 'package_images', 'packages', 'destinations', 'tour_types', 'media', 'settings', 'admin_users'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
