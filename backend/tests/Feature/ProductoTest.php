<?php

use App\Models\Producto;
use App\Models\User;

test('listar productos requiere autenticación', function () {
    $response = $this->getJson('/api/productos');

    $response->assertStatus(401);
});

test('listar productos para un usuario autenticado', function () {
    $user = User::create([
        'name' => 'User',
        'email' => 'user@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    Producto::create([
        'nombre' => 'Producto 1',
        'categoria' => 'Fertilizantes',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    Producto::create([
        'nombre' => 'Producto 2',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 5,
    ]);

    $response = $this->actingAs($user)->getJson('/api/productos');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('permite crear un nuevo producto a un admin', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $response = $this->actingAs($admin)->postJson('/api/productos', [
        'nombre' => 'Semilla de Soja VIP',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.nombre', 'Semilla de Soja VIP');

    $this->assertDatabaseHas('productos', [
        'nombre' => 'Semilla de Soja VIP',
    ]);
});

test('crear producto falla sin nombre', function () {
    $user = User::create([
        'name' => 'User',
        'email' => 'user_noname@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $response = $this->actingAs($user)->postJson('/api/productos', [
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['nombre']);
});

test('mostrar un producto especifico', function () {
    $user = User::create([
        'name' => 'User',
        'email' => 'user2@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $producto = Producto::create([
        'nombre' => 'Fertilizante NPK',
        'categoria' => 'Fertilizantes',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    $response = $this->actingAs($user)->getJson("/api/productos/{$producto->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.nombre', 'Fertilizante NPK');
});

test('admin puede actualizar un producto', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin2@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $producto = Producto::create([
        'nombre' => 'Insecticida A',
        'categoria' => 'Agroquímicos',
        'unidad_medida' => 'litros',
        'stock_minimo' => 5,
    ]);

    $response = $this->actingAs($admin)->putJson("/api/productos/{$producto->id}", [
        'nombre' => 'Insecticida A Modificado',
        'unidad_medida' => 'litros',
        'stock_minimo' => 15,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.nombre', 'Insecticida A Modificado');
});

test('operador no puede eliminar un producto', function () {
    $operador = User::create([
        'name' => 'Op',
        'email' => 'op@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $producto = Producto::create([
        'nombre' => 'Herbicida B',
        'categoria' => 'Agroquímicos',
        'unidad_medida' => 'litros',
        'stock_minimo' => 5,
    ]);

    $response = $this->actingAs($operador)->deleteJson("/api/productos/{$producto->id}");

    $response->assertStatus(403);
});

test('admin puede eliminar un producto', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin3@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $producto = Producto::create([
        'nombre' => 'Fungicida C',
        'categoria' => 'Agroquímicos',
        'unidad_medida' => 'litros',
        'stock_minimo' => 5,
    ]);

    $response = $this->actingAs($admin)->deleteJson("/api/productos/{$producto->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
});
