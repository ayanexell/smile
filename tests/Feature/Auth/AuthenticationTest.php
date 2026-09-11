<?php
use Illuminate\Support\Facades\Log;
use App\Models\Departemens;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roles;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users can not authenticate with unregistered email', function () {
    $response = $this->post(route('login.store'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users can not authenticate with unregistered NIK', function () {
    // Jika login menggunakan NIK
    $response = $this->post(route('login.store'), [
        'nik' => '9999999999999999',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors();

    $this->assertGuest();
});

test('super admin can authenticate and access dashboard', function () {
    $role = Roles::factory()->superAdmin()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    // Verify super admin role
    expect(auth()->user()->role->nama_role)->toBe('Super Admin');
});

test('admin can authenticate and access dashboard', function () {
    $role = Roles::factory()->admin()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    // Verify admin role
    expect(auth()->user()->role->nama_role)->toBe('Admin');
});

test('regular user can authenticate and access dashboard', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    // Verify regular user role
    expect(auth()->user()->role->nama_role)->toBe('User');
});

test('authenticated users can access dashboard with role-based data', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee("Dashboard");
});

test('unverified user can still login but may have limited access', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->unverified()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();
});

test('users can logout', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('authenticated users have correct session data', function () {
    $role = Roles::factory()->admin()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    // Verify session has user data
    expect(auth()->user()->id_user)->toBe($user->id_user);
    expect(auth()->user()->role_id)->toBe($role->id_role);
    expect(auth()->user()->nama_lengkap)->toBe($user->nama_lengkap);
    expect(auth()->user()->email)->toBe($user->email);
});

test('remember me functionality works', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    // Check if remember token is set
    $this->assertNotNull(auth()->user()->remember_token);
});

test('throttle login attempts', function () {
    $role = Roles::factory()->user()->create();
    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    // Lakukan 5 percobaan gagal berturut-turut
    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    // Percobaan ke-6 seharusnya di-throttle
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    // Cek apakah throttle memberikan 429 atau session error
    if ($response->status() === 429) {
        // Throttle bawaan Laravel → 429 Too Many Requests
        $this->assertTrue(true);
    } else {
        // Jika Anda menggunakan custom handler yang menambahkan error manual
        $response->assertSessionHasErrors('email');
    }

    $this->assertGuest();
});

test('users with departemen can authenticate', function () {
    $role = Roles::factory()->user()->create();
    $departemen = \App\Models\Departemens::factory()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'departemen_id' => $departemen->id_departemen,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    // Verify user has departemen
    expect(auth()->user()->departemen)->not->toBeNull();
    expect(auth()->user()->departemen->id_departemen)->toBe($departemen->id_departemen);
});

test('users without departemen can authenticate', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->withoutDepartemen()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    // Verify user has no departemen
    expect(auth()->user()->departemen)->toBeNull();
});

test('redirect to intended page after login', function () {
    $role = Roles::factory()->user()->create();

    $user = User::factory()->create([
        'role_id' => $role->id_role,
        'password' => bcrypt('password'),
    ]);

    // Try to access protected page
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    // Login
    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'intended' => route('dashboard'),
    ]);

    // Should redirect to intended page
    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});

test('multiple users with same role can authenticate', function () {
    $role = Roles::factory()->user()->create();

    $users = User::factory()->count(3)->create([
        'role_id' => $role->id_role,
        'password' => Hash::make('password'),
    ]);

    foreach ($users as $user) {
        // Reset session dan state sebelum setiap login
        $this->post(route('logout'));
        $this->assertGuest();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home', absolute: false));
        $this->assertAuthenticated();

        expect(auth()->user()->id_user)->toBe($user->id_user);

        // Logout setelah pengecekan untuk membersihkan state
        $this->post(route('logout'));
        $this->assertGuest();
    }
});
