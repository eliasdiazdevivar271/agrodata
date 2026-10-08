<?php

use App\Models\Producto;
use App\Models\User;

test('admin puede acceder a /api/admin-only', function () {
    $admin = User::create([
        'name' => 'Admin Policy',
        'email' => 'admin_pol@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $response = $this->actingAs($admin)->getJson('/api/admin-only');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Acceso concedido a panel de administración.']);
});

test('operador recibe 403 en /api/admin-only', function () {
    $operador = User::create([
        'name' => 'Operador Policy',
        'email' => 'op_pol@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $response = $this->actingAs($operador)->getJson('/api/admin-only');

    $response->assertStatus(403);
});

test('operador no puede eliminar productos', function () {
    $operador = User::create([
        'name' => 'Operador Policy 2',
        'email' => 'op_pol2@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $producto = Producto::create([
        'nombre' => 'Producto Policy Test',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    $response = $this->actingAs($operador)->deleteJson("/api/productos/{$producto->id}");

    $response->assertStatus(403);
});
