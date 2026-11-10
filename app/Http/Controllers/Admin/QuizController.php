<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuizStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Hero quiz editor: steps (questions) and their answer options. Order = sort_order. */
class QuizController extends CrudController
{
    protected string $model = QuizStep::class;

    protected string $view = 'quiz';

    protected string $route = 'admin.quiz';

    protected string $singular = 'quiz question';

    protected string $plural = 'Quiz';

    public const ICONS = ['sun', 'flower', 'leaf', 'snowflake', 'bus', 'car', 'user', 'users', 'question', 'check', 'x', 'wallet', 'thumb', 'scale', 'heart', 'sparkles', 'mountain', 'map', 'compass', 'calendar', 'clock'];

    protected function rules(?Model $item): array
    {
        return [
            'question' => ['required', 'string', 'max:200'],
            'label' => ['required', 'string', 'max:60'],
            'field_key' => ['nullable', 'alpha_dash', 'max:60', Rule::unique('quiz_steps', 'field_key')->ignore($item?->id)],
            'type' => ['required', 'in:tiles,select'],
            'is_active' => ['boolean'],
            'options' => ['required', 'array', 'min:1', 'max:40'],
            'options.*.label' => ['required', 'string', 'max:100'],
            'options.*.icon' => ['nullable', 'string', 'max:40'],
            'options.*.is_active' => ['nullable'],
        ];
    }

    protected function columns(): array
    {
        return [
            'Question' => fn ($s) => $s->question,
            'Type' => fn ($s) => $s->type === 'tiles' ? 'Tiles (auto-advance)' : 'Dropdown + Next',
            'Options' => fn ($s) => $s->options()->count(),
            'Active' => fn ($s) => $s->is_active ? 'Yes' : 'No',
        ];
    }

    protected function formData(?Model $item): array
    {
        return ['icons' => self::ICONS, 'options' => $item?->exists ? $item->options()->get(['label', 'icon', 'is_active'])->map(fn ($o) => ['label' => $o->label, 'icon' => $o->icon ?? '', 'is_active' => (bool) $o->is_active])->all() : []];
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        // The field key is the answer's identity (and part of the CRM field name): fixed once created.
        $data['field_key'] = $item?->field_key ?: Str::slug($data['field_key'] ?: $data['label'], '_');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $item?->sort_order ?? (QuizStep::max('sort_order') + 1);
        unset($data['options']);

        return $data;
    }

    protected function after(Model $item, Request $request): void
    {
        $item->options()->delete();
        foreach (array_values($request->input('options', [])) as $i => $o) {
            if (blank($o['label'] ?? null)) {
                continue;
            }
            $item->options()->create(['label' => trim($o['label']), 'icon' => $o['icon'] ?: null, 'sort_order' => $i + 1, 'is_active' => ! empty($o['is_active']) && $o['is_active'] !== '0']);
        }
    }
}
