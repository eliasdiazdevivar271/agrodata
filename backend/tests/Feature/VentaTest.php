<?php

use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\User;

test('permite crear venta con logica FIFO al haber stock suficiente', function () {
    $user = User::create([
        'name' => 'Vendedor',
        'email' => 'vendedor@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $cliente = Cliente::create([
        'nombre' => 'Estancia El Paraíso',
        'ruc' => '80012345-6',
    ]);

    $producto = Producto::create([
        'nombre' => 'Semilla Maíz Híbrido',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 10,
    ]);

    // Lotes con diferentes vencimientos
    $lote1 = Lote::create([
        'producto_id' => $producto->id,
        'codigo_lote' => 'LOT-001',
        'cantidad_inicial' => 10,
        'cantidad_actual' => 10,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(10)->toDateString(),
    ]);

    $lote2 = Lote::create([
        'producto_id' => $producto->id,
        'codigo_lote' => 'LOT-002',
        'cantidad_inicial' => 20,
        'cantidad_actual' => 20,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(20)->toDateString(),
    ]);

    $response = $this->actingAs($user)->postJson('/api/ventas', [
        'cliente_id' => $cliente->id,
        'fecha' => now()->toDateString(),
        'detalles' => [
            [
                'producto_id' => $producto->id,
                'cantidad' => 15,
                'precio_unitario' => 50000,
            ],
        ],
    ]);

    $response->assertStatus(201);

    // Verificación FIFO: lote 1 consumido a 0, lote 2 reducido a 15
    $this->assertDatabaseHas('lotes', ['id' => $lote1->id, 'cantidad_actual' => 0]);
    $this->assertDatabaseHas('lotes', ['id' => $lote2->id, 'cantidad_actual' => 15]);
});

test('valida stock insuficiente al intentar vender mas del stock disponible', function () {
    $user = User::create([
        'name' => 'Vendedor 2',
        'email' => 'vendedor2@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $producto = Producto::create([
        'nombre' => 'Fertilizante Orgánico',
        'categoria' => 'Fertilizantes',
        'unidad_medida' => 'kg',
        'stock_minimo' => 5,
    ]);

    Lote::create([
        'producto_id' => $producto->id,
        'codigo_lote' => 'LOT-003',
        'cantidad_inicial' => 5,
        'cantidad_actual' => 5,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(10)->toDateString(),
    ]);

    $response = $this->actingAs($user)->postJson('/api/ventas', [
        'fecha' => now()->toDateString(),
        'detalles' => [
            [
                'producto_id' => $producto->id,
                'cantidad' => 50,
                'precio_unitario' => 50000,
            ],
        ],
    ]);

    $response->assertStatus(422);
});

test('crear venta calcula subtotales y total correctamente', function () {
    $user = User::create([
        'name' => 'Vendedor Subtotal',
        'email' => 'vendedor_sub@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $cliente = Cliente::create([
        'nombre' => 'Cliente Test',
        'ruc' => '123456-7',
    ]);

    $prod1 = Producto::create([
        'nombre' => 'Producto 1',
        'categoria' => 'Fertilizantes',
        'unidad_medida' => 'kg',
        'stock_minimo' => 5,
    ]);

    $prod2 = Producto::create([
        'nombre' => 'Producto 2',
        'categoria' => 'Semillas',
        'unidad_medida' => 'kg',
        'stock_minimo' => 5,
    ]);

    Lote::create([
        'producto_id' => $prod1->id,
        'codigo_lote' => 'LOT-S1',
        'cantidad_inicial' => 10,
        'cantidad_actual' => 10,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(30)->toDateString(),
    ]);

    Lote::create([
        'producto_id' => $prod2->id,
        'codigo_lote' => 'LOT-S2',
        'cantidad_inicial' => 10,
        'cantidad_actual' => 10,
        'fecha_ingreso' => now()->toDateString(),
        'fecha_vencimiento' => now()->addDays(30)->toDateString(),
    ]);

    $response = $this->actingAs($user)->postJson('/api/ventas', [
        'cliente_id' => $cliente->id,
        'fecha' => now()->toDateString(),
        'detalles' => [
            [
                'producto_id' => $prod1->id,
                'cantidad' => 2,
                'precio_unitario' => 15000,
            ],
            [
                'producto_id' => $prod2->id,
                'cantidad' => 3,
                'precio_unitario' => 20000,
            ],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.total', 90000);

    $this->assertDatabaseHas('ventas', ['total' => 90000]);
    $this->assertDatabaseHas('detalle_ventas', ['subtotal' => 30000]);
    $this->assertDatabaseHas('detalle_ventas', ['subtotal' => 60000]);
});

test('crear venta requiere al menos un detalle', function () {
    $user = User::create([
        'name' => 'Vendedor No Details',
        'email' => 'vendedor_nodetail@agrodata.com',
        'password' => bcrypt('password'),
        'rol' => 'operador',
    ]);

    $response = $this->actingAs($user)->postJson('/api/ventas', [
        'fecha' => now()->toDateString(),
        'detalles' => [],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['detalles']);
});
