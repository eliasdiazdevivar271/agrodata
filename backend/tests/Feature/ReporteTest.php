<?php

use App\Models\Lote;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;

test('operador no tiene acceso a reportes de admin', function () {
    $operador = User::create([
        'name' => 'Operador Reportes',
        'email' => 'oprep@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $response = $this->actingAs($operador)->getJson('/api/reportes/dashboard');

    $response->assertStatus(403);
});

test('admin puede obtener el dashboard de reportes', function () {
    $admin = User::create([
        'name' => 'Admin Reportes',
        'email' => 'adminrep@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $response = $this->actingAs($admin)->getJson('/api/reportes/dashboard');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'productos_por_vencer',
            'mermas_del_mes',
            'productos_bajo_stock',
            'ventas_del_mes',
        ]);
});

test('admin puede consultar productos por vencer', function () {
    $admin = User::create([
        'name' => 'Admin Reportes 2',
        'email' => 'adminrep2@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $producto = Producto::create([
        'nombre' => 'Semilla Girasol',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    Lote::create([
        'producto_id' => $producto->id,
        'codigo_lote' => 'LOT-PV',
        'cantidad_inicial' => 100,
        'cantidad_actual' => 100,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(5)->toDateString(),
    ]);

    $response = $this->actingAs($admin)->getJson('/api/reportes/por-vencer?dias=30');

    $response->assertStatus(200);
});

test('admin puede consultar mermas del mes', function () {
    $admin = User::create([
        'name' => 'Admin Reportes 3',
        'email' => 'adminrep3@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    $producto = Producto::create([
        'nombre' => 'Fertilizante A',
        'categoria' => 'Fertilizantes',
        'unidad_medida' => 'kg',
        'stock_minimo' => 5,
    ]);

    $lote = Lote::create([
        'producto_id' => $producto->id,
        'codigo_lote' => 'LOT-MERMA',
        'cantidad_inicial' => 50,
        'cantidad_actual' => 48,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(30)->toDateString(),
    ]);

    Movimiento::create([
        'lote_id' => $lote->id,
        'user_id' => $admin->id,
        'tipo' => 'merma',
        'cantidad' => 2,
        'motivo' => 'Bolsa rota',
    ]);

    $response = $this->actingAs($admin)->getJson('/api/reportes/mermas');

    $response->assertStatus(200);
});

test('admin puede consultar productos bajo stock minimo', function () {
    $admin = User::create([
        'name' => 'Admin Reportes 4',
        'email' => 'adminrep4@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'admin',
    ]);

    Producto::create([
        'nombre' => 'Producto Bajo Stock',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 100,
    ]);

    $response = $this->actingAs($admin)->getJson('/api/reportes/bajo-stock');

    $response->assertStatus(200);
});
