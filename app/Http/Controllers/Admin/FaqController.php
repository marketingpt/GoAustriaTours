<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController extends CrudController
{
    protected string $model = Faq::class;

    protected string $view = 'faqs';

    protected string $route = 'admin.faqs';

    protected string $singular = 'FAQ';

    protected string $plural = 'FAQs';

    protected function rules(?Model $item): array
    {
        return ['question' => ['required', 'string', 'max:200'], 'answer' => ['required', 'string', 'max:3000'], 'is_active' => ['boolean']];
    }

    protected function columns(): array
    {
        return ['Question' => fn ($f) => $f->question, 'Answer' => fn ($f) => Str::limit($f->answer, 80), 'Active' => fn ($f) => $f->is_active ? 'Yes' : 'No'];
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $item?->sort_order ?? (Faq::max('sort_order') + 1);

        return $data;
    }
}
