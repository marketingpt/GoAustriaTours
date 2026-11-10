<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Small generic CRUD for simple admin lists (tour types, destinations, testimonials, FAQs, packages).
 * Subclasses set the properties below and implement rules(). Views: admin/<view>/form.blade.php,
 * and the shared admin/crud/index|form wrappers.
 */
abstract class CrudController extends Controller
{
    protected string $model;

    protected string $view;       // folder under resources/views/admin

    protected string $route;      // route name prefix, e.g. "admin.tour-types"

    protected string $singular;   // "tour type"

    protected string $plural;     // "Tour types"

    protected bool $sortable = true;

    abstract protected function rules(?Model $item): array;

    /** Columns for the index: label => fn(Model): string|\Illuminate\Contracts\Support\Htmlable */
    abstract protected function columns(): array;

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        return $data;
    }

    protected function after(Model $item, Request $request): void {}

    protected function query()
    {
        $q = ($this->model)::query();

        return $this->sortable ? $q->orderBy('sort_order')->orderBy('id') : $q->latest('id');
    }

    protected function formData(?Model $item): array
    {
        return [];
    }

    public function index()
    {
        return view('admin.crud.index', [
            'items' => $this->query()->get(), 'columns' => $this->columns(), 'route' => $this->route,
            'plural' => $this->plural, 'singular' => $this->singular, 'sortable' => $this->sortable,
            'extraActions' => $this->extraActions(),
        ]);
    }

    protected function extraActions(): array
    {
        return [];
    }

    public function create()
    {
        return view('admin.crud.form', $this->formVars(new ($this->model)));
    }

    public function edit(string $id)
    {
        return view('admin.crud.form', $this->formVars(($this->model)::findOrFail($id)));
    }

    private function formVars(Model $item): array
    {
        return ['item' => $item, 'route' => $this->route, 'view' => $this->view, 'singular' => $this->singular, 'plural' => $this->plural] + $this->formData($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(null));
        $item = ($this->model)::create($this->transform($data, $request, null));
        $this->after($item, $request);

        return redirect()->route("{$this->route}.index")->with('status', ucfirst($this->singular).' created.');
    }

    public function update(Request $request, string $id)
    {
        $item = ($this->model)::findOrFail($id);
        $data = $request->validate($this->rules($item));
        $item->update($this->transform($data, $request, $item));
        $this->after($item, $request);

        return redirect()->route("{$this->route}.index")->with('status', ucfirst($this->singular).' saved.');
    }

    public function destroy(string $id)
    {
        ($this->model)::findOrFail($id)->delete();

        return redirect()->route("{$this->route}.index")->with('status', ucfirst($this->singular).' deleted.');
    }

    /** Move up/down by re-numbering sort_order sequentially. */
    public function move(Request $request, string $id)
    {
        abort_unless($this->sortable, 404);
        $ids = $this->query()->pluck('id')->all();
        $i = array_search((int) $id, $ids, true);
        $j = $request->input('dir') === 'up' ? $i - 1 : $i + 1;
        if ($i !== false && isset($ids[$j])) {
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $n => $key) {
                ($this->model)::whereKey($key)->update(['sort_order' => $n + 1]);
            }
        }

        return back();
    }

    protected function checkbox(Request $request, string $key): bool
    {
        return $request->boolean($key);
    }
}
