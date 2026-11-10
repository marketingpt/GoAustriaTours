<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use App\Models\Package;
use App\Models\TourType;
use App\Support\Html;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageController extends CrudController
{
    protected string $model = Package::class;

    protected string $view = 'packages';

    protected string $route = 'admin.packages';

    protected string $singular = 'package';

    protected string $plural = 'Packages';

    protected function rules(?Model $item): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'alpha_dash', 'max:160', Rule::unique('packages', 'slug')->ignore($item?->id)],
            'tour_type_id' => ['nullable', 'exists:tour_types,id'],
            'destination_id' => ['nullable', 'exists:destinations,id'],
            'nights' => ['required', 'integer', 'min:1', 'max:60'],
            'price_from' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'currency' => ['required', 'string', 'size:3'],
            'price_note' => ['nullable', 'string', 'max:160'],
            'summary' => ['nullable', 'string', 'max:400'],
            'description' => ['nullable', 'string', 'max:50000'],
            'highlights' => ['nullable', 'string', 'max:3000'],
            'inclusions' => ['nullable', 'string', 'max:3000'],
            'exclusions' => ['nullable', 'string', 'max:3000'],
            'itinerary' => ['nullable', 'array', 'max:60'],
            'itinerary.*.title' => ['nullable', 'string', 'max:160'],
            'itinerary.*.body' => ['nullable', 'string', 'max:2000'],
            'faqs' => ['nullable', 'array', 'max:30'],
            'faqs.*.question' => ['nullable', 'string', 'max:200'],
            'faqs.*.answer' => ['nullable', 'string', 'max:2000'],
            'media_id' => ['nullable', 'exists:media,id'],
            'gallery' => ['nullable', 'array', 'max:30'],
            'gallery.*' => ['exists:media,id'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'og_media_id' => ['nullable', 'exists:media,id'],
            'noindex' => ['boolean'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_sample' => ['boolean'],
        ];
    }

    protected function columns(): array
    {
        return [
            'Title' => fn ($p) => $p->title.($p->is_sample ? ' (sample)' : ''),
            'Type' => fn ($p) => $p->tourType?->name ?? '-',
            'Nights' => fn ($p) => $p->nights,
            'From' => fn ($p) => $p->priceLabel() ?? '-',
            'Status' => fn ($p) => ($p->is_published ? 'Published' : 'Draft').($p->is_featured ? ' · Featured' : ''),
        ];
    }

    protected function extraActions(): array
    {
        return ['Duplicate' => ['admin.packages.duplicate'], 'Publish/Unpublish' => ['admin.packages.publish'], 'Feature' => ['admin.packages.feature']];
    }

    protected function formData(?Model $item): array
    {
        return [
            'tourTypes' => TourType::orderBy('sort_order')->pluck('name', 'id')->all(),
            'destinations' => Destination::orderBy('sort_order')->pluck('name', 'id')->all(),
            'galleryIds' => $item?->exists ? $item->gallery()->pluck('media.id')->all() : [],
        ];
    }

    private function lines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))->map('trim')->filter()->values()->all();
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['description'] = Html::clean($data['description'] ?? null);
        foreach (['highlights', 'inclusions', 'exclusions'] as $k) {
            $data[$k] = $this->lines($data[$k] ?? '');
        }
        $data['itinerary'] = collect($data['itinerary'] ?? [])->filter(fn ($d) => filled($d['title'] ?? null))->values()->all();
        $data['faqs'] = collect($data['faqs'] ?? [])->filter(fn ($d) => filled($d['question'] ?? null) && filled($d['answer'] ?? null))->values()->all();
        foreach (['noindex', 'is_published', 'is_featured', 'is_sample'] as $k) {
            $data[$k] = $request->boolean($k);
        }
        $data['currency'] = strtoupper($data['currency']);
        $data['sort_order'] = $item?->sort_order ?? (Package::max('sort_order') + 1);
        unset($data['gallery']);

        return $data;
    }

    protected function after(Model $item, Request $request): void
    {
        $sync = [];
        foreach (array_values($request->input('gallery', [])) as $i => $id) {
            $sync[(int) $id] = ['sort_order' => $i + 1];
        }
        $item->gallery()->sync($sync);
    }

    public function duplicate(string $id)
    {
        $src = Package::with('gallery')->findOrFail($id);
        $copy = $src->replicate();
        $copy->title = $src->title.' (copy)';
        $copy->slug = $this->uniqueSlug($src->slug.'-copy');
        $copy->is_published = false;
        $copy->is_featured = false;
        $copy->sort_order = Package::max('sort_order') + 1;
        $copy->save();
        $copy->gallery()->sync($src->gallery->mapWithKeys(fn ($m) => [$m->id => ['sort_order' => $m->pivot->sort_order]])->all());

        return redirect()->route('admin.packages.edit', $copy)->with('status', 'Package duplicated as a draft. Edit and publish when ready.');
    }

    private function uniqueSlug(string $slug): string
    {
        $base = $slug;
        $n = 2;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }

    public function publish(string $id)
    {
        $p = Package::findOrFail($id);
        $p->update(['is_published' => ! $p->is_published]);

        return back()->with('status', $p->is_published ? 'Package published.' : 'Package unpublished.');
    }

    public function feature(string $id)
    {
        $p = Package::findOrFail($id);
        $p->update(['is_featured' => ! $p->is_featured]);

        return back()->with('status', $p->is_featured ? 'Package featured on the home page.' : 'Package removed from featured.');
    }
}
