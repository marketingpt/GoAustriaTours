<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestimonialController extends CrudController
{
    protected string $model = Testimonial::class;

    protected string $view = 'testimonials';

    protected string $route = 'admin.testimonials';

    protected string $singular = 'testimonial';

    protected string $plural = 'Testimonials';

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:1000'],
            'is_placeholder' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    protected function columns(): array
    {
        return ['Name' => fn ($t) => $t->name, 'Quote' => fn ($t) => Str::limit($t->quote, 70), 'Placeholder' => fn ($t) => $t->is_placeholder ? 'Yes' : 'No', 'Active' => fn ($t) => $t->is_active ? 'Yes' : 'No'];
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        $data['is_placeholder'] = $request->boolean('is_placeholder');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $item?->sort_order ?? (Testimonial::max('sort_order') + 1);

        return $data;
    }
}
