<?php

namespace App\Support;

use App\Models\Media;

/** Builds the $seo array every public page passes to the layout (title, description, canonical, OG, JSON-LD). */
class Seo
{
    public static function make(string $title, ?string $description = null, ?Media $image = null, bool $noindex = false, array $jsonld = [], ?string $canonical = null, bool $fullTitle = false): array
    {
        $brand = (string) setting('brand_name');
        $description = $description ?: (string) setting('default_meta_description');

        if (! $image && ($id = setting('default_og_media_id'))) {
            $image = Media::find($id);
        }

        return [
            'title' => ($fullTitle || str_contains($title, $brand)) ? $title : "{$title} | {$brand}",
            'description' => mb_substr(trim(strip_tags($description)), 0, 300),
            'canonical' => $canonical ?? url()->current(),
            'image' => $image?->url(1600),
            'noindex' => $noindex,
            'jsonld' => $jsonld,
        ];
    }

    public static function organization(): array
    {
        $sameAs = array_values(array_filter([setting('social_facebook'), setting('social_instagram'), setting('social_youtube'), setting('social_tiktok')]));
        $logo = ($id = setting('logo_media_id')) ? Media::find($id)?->url(480) : null;

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => setting('brand_name'),
            'legalName' => setting('legal_name'),
            'url' => url('/'),
            'logo' => $logo,
            'description' => setting('default_meta_description'),
            'telephone' => setting('phone') ?: null,
            'email' => setting('email') ?: null,
            'address' => setting('address') ? ['@type' => 'PostalAddress', 'streetAddress' => setting('address')] : null,
            'areaServed' => 'Austria',
            'sameAs' => $sameAs ?: null,
        ]);
    }

    /** @param array<int,array{0:string,1:string}> $items [name, url] pairs, in order */
    public static function breadcrumbs(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($i, $n) => [
                '@type' => 'ListItem', 'position' => $n + 1, 'name' => $i[0], 'item' => $i[1],
            ])->all(),
        ];
    }

    /** @param iterable<object|array> $faqs items with question/answer */
    public static function faqPage(iterable $faqs): ?array
    {
        $entities = [];
        foreach ($faqs as $f) {
            $q = is_array($f) ? $f['question'] : $f->question;
            $a = is_array($f) ? $f['answer'] : $f->answer;
            $entities[] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($a)]];
        }

        return $entities ? ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities] : null;
    }
}
