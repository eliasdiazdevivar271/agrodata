<?php

use App\Models\User;

test('permite registrar un nuevo usuario', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Juan Perez',
        'email' => 'juan@agrodata.com',
        'password' => 'password123',
        'rol' => 'operador',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['message', 'user', 'token']);

    $this->assertDatabaseHas('users', [
        'email' => 'juan@agrodata.com',
        'rol' => 'operador',
    ]);
});

test('registro falla con email duplicado', function () {
    User::create([
        'name' => 'Usuario Existente',
        'email' => 'duplicado@agrodata.com',
        'password' => bcrypt('password123'),
        'rol' => 'operador',
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Juan Perez',
        'email' => 'duplicado@agrodata.com',
        'password' => 'password123',
        'rol' => 'operador',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('permite iniciar sesion con credenciales correctas', function () {
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'test@agrodata.com',
        'password' => bcrypt('password123'),
        'rol' => 'admin',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@agrodata.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'user', 'token']);
});

test('rechaza inicio de sesion con contraseña incorrecta', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test2@agrodata.com',
        'password' => bcrypt('password123'),
        'rol' => 'operador',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test2@agrodata.com',
        'password' => 'incorrecta',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Credenciales inválidas']);
});

test('permite cerrar sesion al usuario autenticado', function () {
    $user = User::create([
        'name' => 'Logout User',
        'email' => 'logout@agrodata.com',
        'password' => bcrypt('password123'),
        'rol' => 'operador',
    ]);

    $response = $this->actingAs($user)->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Sesión cerrada exitosamente']);
});

test('devuelve la informacion del usuario autenticado', function () {
    $user = User::create([
        'name' => 'Carlos',
        'email' => 'carlos@agrodata.com',
        'password' => bcrypt('password123'),
        'rol' => 'operador',
    ]);

    $response = $this->actingAs($user)->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson(['name' => 'Carlos']);
});
