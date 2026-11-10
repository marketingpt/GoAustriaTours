<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_require_login(): void
    {
        foreach (['/admin', '/admin/enquiries', '/admin/packages', '/admin/settings', '/admin/users', '/admin/media', '/admin/quiz'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'correct-horse-battery']);

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'nope'])->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_login_logout_flow(): void
    {
        AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'correct-horse-battery']);

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'correct-horse-battery'])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated('admin');
        $this->get('/admin')->assertOk();

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_login_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'bad'])->assertSessionHasErrors('email');
        }
        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'bad'])->assertStatus(429);
    }

    public function test_password_is_hashed(): void
    {
        $u = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'correct-horse-battery']);

        $this->assertNotSame('correct-horse-battery', $u->getRawOriginal('password'));
    }

    public function test_cannot_delete_yourself_or_last_admin(): void
    {
        $me = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'correct-horse-battery']);
        $other = AdminUser::create(['name' => 'B', 'email' => 'b@example.com', 'password' => 'correct-horse-battery']);

        $this->actingAs($me, 'admin')->delete(route('admin.users.destroy', $me->id))->assertSessionHasErrors('user');
        $this->assertDatabaseHas('admin_users', ['id' => $me->id]);

        $this->actingAs($me, 'admin')->delete(route('admin.users.destroy', $other->id))->assertRedirect();
        $this->assertDatabaseMissing('admin_users', ['id' => $other->id]);

        // now the only admin: still protected
        $this->actingAs($me, 'admin')->delete(route('admin.users.destroy', $me->id))->assertSessionHasErrors('user');
    }
}
