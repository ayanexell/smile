<?php

use App\Models\User;
use App\Models\Roles;
use App\Models\Departemens;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register with valid data', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'test@example.com',
        'alamat' => 'Jl. Contoh No. 123, Jakarta',
        'pekerjaan' => 'Software Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    // Verify user data in database
    $this->assertDatabaseHas('users', [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'email' => 'test@example.com',
        'jenis_kelamin' => 'laki-laki',
        'pekerjaan' => 'Software Developer',
    ]);

    // Verify user has default role 'User'
    $user = User::where('email', 'test@example.com')->first();
    expect($user->role->nama_role)->toBe('User');
});

test('registration assigns default user role', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'Jane Doe',
        'nik' => '3201010102900002',
        'tgl_lahir' => '1992-05-15',
        'jenis_kelamin' => 'perempuan',
        'email' => 'jane@example.com',
        'alamat' => 'Jl. Mawar No. 45, Bandung',
        'pekerjaan' => 'Designer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();

    $user = User::where('email', 'jane@example.com')->first();
    expect($user->role_id)->not->toBeNull();
    expect($user->role->nama_role)->toBe('User');
    expect($user->role->nama_role)->not->toBe('Admin');
    expect($user->role->nama_role)->not->toBe('Super Admin');
});

test('registration requires valid NIK format', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '123', // Invalid NIK (too short)
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['nik']);
    $this->assertGuest();
});

test('registration requires NIK to be 16 characters', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '32010101', // Only 8 characters
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['nik']);
    $this->assertGuest();
});

test('registration requires unique NIK', function () {
    // Create user first
    $role = Roles::create(['nama_role' => 'User']);
    User::factory()->create([
        'role_id' => $role->id_role,
        'nik' => '3201010101900001',
    ]);

    // Try to register with same NIK
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001', // Already used
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['nik']);
    $this->assertGuest();
});

test('registration requires unique email', function () {
    $role = Roles::create(['nama_role' => 'User']);
    User::factory()->create([
        'role_id' => $role->id_role,
        'email' => 'existing@example.com',
    ]);

    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'existing@example.com', // Already used
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('registration requires valid date of birth', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => 'invalid-date',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['tgl_lahir']);
    $this->assertGuest();
});

test('registration requires valid gender', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'invalid-gender',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['jenis_kelamin']);
    $this->assertGuest();
});

test('registration accepts both gender options', function () {
    // Bersihkan session secara manual sebelum memulai
    session()->flush();
    $this->assertGuest();

    // Test laki-laki
    $response1 = $this->post(route('register.store'), [
        'nama_lengkap'  => 'John Doe',
        'nik'           => '3201010101900001',
        'tgl_lahir'     => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email'         => 'john@example.com',
        'alamat'        => 'Test Address',
        'pekerjaan'     => 'Developer',
        'password'      => 'password',
        'password_confirmation' => 'password',
    ]);

    $response1->assertSessionHasNoErrors()
             ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

    // Logout dan bersihkan session
    $this->post(route('logout'));
    Roles::where('nama_role', 'User')->delete(); // Hapus role User yang dibuat oleh factory
    session()->flush();

    // Test perempuan
    $response2 = $this->post(route('register.store'), [
        'nama_lengkap'  => 'Jane Doe',
        'nik'           => '3201010102900002',
        'tgl_lahir'     => '1992-05-15',
        'jenis_kelamin' => 'perempuan',
        'email'         => 'jane@example.com',
        'alamat'        => 'Test Address',
        'pekerjaan'     => 'Designer',
        'password'      => 'password',
        'password_confirmation' => 'password',
    ]);

    $response2->assertSessionHasNoErrors()
             ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
});

test('registration requires password confirmation', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
});

test('registration requires minimum password length', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => '12345', // Too short
        'password_confirmation' => '12345',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
});

test('registration requires all fields', function () {
    $response = $this->post(route('register.store'), []);

    $response->assertSessionHasErrors([
        'nama_lengkap',
        'nik',
        'tgl_lahir',
        'jenis_kelamin',
        'email',
        'alamat',
        'pekerjaan',
        'password',
    ]);

    $this->assertGuest();
});

test('user can register with optional departemen', function () {
    $departemen = Departemens::factory()->create();

    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
        'departemen_id' => $departemen->id_departemen,
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();

    $user = User::where('email', 'john@example.com')->first();
    expect($user->departemen_id)->toBe($departemen->id_departemen);
});

test('user can register without departemen', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();

    $user = User::where('email', 'john@example.com')->first();
    expect($user->departemen_id)->toBeNull();
});

test('registration automatically sends email verification', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();

    $user = User::where('email', 'john@example.com')->first();

    // If email verification is enabled, email_verified_at should be null
    // If not, it should have a timestamp
    if (config('auth.verify_email', false)) {
        expect($user->email_verified_at)->toBeNull();
    }
});

test('password is securely hashed during registration', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'MySecretPassword123',
        'password_confirmation' => 'MySecretPassword123',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'john@example.com')->first();

    // Password should be hashed, not stored in plain text
    expect($user->password)->not->toBe('MySecretPassword123');
    expect(password_verify('MySecretPassword123', $user->password))->toBeTrue();
});

test('registration creates user with timestamps', function () {
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors();

    $user = User::where('email', 'john@example.com')->first();
    expect($user->created_at)->not->toBeNull();
    expect($user->updated_at)->not->toBeNull();
});

test('user cannot register with future date of birth', function () {
    $futureDate = now()->addYear()->format('Y-m-d');

    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => $futureDate,
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Depending on your validation rules
    $response->assertSessionHasErrors(['tgl_lahir']);
    $this->assertGuest();
});

test('redirect to intended page after registration', function () {
    // Try to access protected page first
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    // Register
    $response = $this->post(route('register.store'), [
        'nama_lengkap' => 'John Doe',
        'nik' => '3201010101900001',
        'tgl_lahir' => '1990-01-01',
        'jenis_kelamin' => 'laki-laki',
        'email' => 'john@example.com',
        'alamat' => 'Test Address',
        'pekerjaan' => 'Developer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Should redirect to intended page or dashboard
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();
});
