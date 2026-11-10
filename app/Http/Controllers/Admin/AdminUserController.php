<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends CrudController
{
    protected string $model = AdminUser::class;

    protected string $view = 'users';

    protected string $route = 'admin.users';

    protected string $singular = 'admin user';

    protected string $plural = 'Admin users';

    protected bool $sortable = false;

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('admin_users', 'email')->ignore($item?->id)],
            'password' => [$item ? 'nullable' : 'required', 'string', 'min:10', 'confirmed'],
        ];
    }

    protected function columns(): array
    {
        return ['Name' => fn ($u) => $u->name.($u->id === auth('admin')->id() ? ' (you)' : ''), 'Email' => fn ($u) => $u->email, 'Added' => fn ($u) => $u->created_at->format('j M Y')];
    }

    protected function transform(array $data, Request $request, ?Model $item): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']); // keep the current password
        }

        return $data;
    }

    public function destroy(string $id)
    {
        $user = AdminUser::findOrFail($id);

        if ($user->id === auth('admin')->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }
        if (AdminUser::count() <= 1) {
            return back()->withErrors(['user' => 'You cannot delete the last admin.']);
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Admin user deleted.');
    }
}
