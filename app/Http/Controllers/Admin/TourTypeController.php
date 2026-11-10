<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TourTypeController extends CrudController
{
    protected string $model = TourType::class;

    protected string $view = 'tour-types';

    protected string $route = 'admin.tour-types';

    protected string $singular = 'tour type';

    protected string $plural = 'Tour types';

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'alpha_dash', 'max:120', Rule::unique('tour_types', 'slug')->ignore($item?->id)],
            'tagline' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:40'],
            'media_id' => ['nullable', 'exists:media,id'],
            'is_active' => ['boolean'],
        ];
    }

    protected function columns(): array
    {
        return ['Name' => fn ($t) => $t->name, 'Slug' => fn ($t) => $t->slug, 'Packages' => fn ($t) => $t->packages()->count(), 'Active' => fn ($t) => $t->is_active ? 'Yes' : 'No'];
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $item?->sort_order ?? (TourType::max('sort_order') + 1);

        return $data;
    }
}
