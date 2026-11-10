<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use App\Support\Html;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DestinationController extends CrudController
{
    protected string $model = Destination::class;

    protected string $view = 'destinations';

    protected string $route = 'admin.destinations';

    protected string $singular = 'destination';

    protected string $plural = 'Destinations';

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'alpha_dash', 'max:120', Rule::unique('destinations', 'slug')->ignore($item?->id)],
            'tagline' => ['nullable', 'string', 'max:160'],
            'intro' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:30000'],
            'media_id' => ['nullable', 'exists:media,id'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'noindex' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    protected function columns(): array
    {
        return ['Name' => fn ($d) => $d->name, 'Slug' => fn ($d) => $d->slug, 'Packages' => fn ($d) => $d->packages()->count(), 'Active' => fn ($d) => $d->is_active ? 'Yes' : 'No'];
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['body'] = Html::clean($data['body'] ?? null);
        $data['is_active'] = $request->boolean('is_active');
        $data['noindex'] = $request->boolean('noindex');
        $data['sort_order'] = $item?->sort_order ?? (Destination::max('sort_order') + 1);

        return $data;
    }
}
