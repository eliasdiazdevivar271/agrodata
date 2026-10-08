<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgroDataApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_login_and_get_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'admin@agrodata.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'rol'],
                'token',
            ]);
    }

    public function test_authenticated_admin_can_access_dashboard_reports(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/reportes/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'productos_por_vencer',
                'mermas_del_mes',
                'productos_bajo_stock',
                'ventas_del_mes' => ['total'],
            ]);
    }

    public function test_operador_cannot_access_admin_reports_or_purchases(): void
    {
        $operador = User::where('email', 'operador@agrodata.com')->first();

        // Intento de acceso a reportes financieros
        $responseReportes = $this->actingAs($operador, 'sanctum')->getJson('/api/reportes/dashboard');
        $responseReportes->assertStatus(403)
            ->assertJsonPath('message', 'No tiene permisos suficientes para acceder a este recurso.');

        // Intento de acceso a compras
        $responseCompras = $this->actingAs($operador, 'sanctum')->getJson('/api/compras');
        $responseCompras->assertStatus(403);
    }

    public function test_operador_can_perform_sales(): void
    {
        $operador = User::where('email', 'operador@agrodata.com')->first();
        $cliente = Cliente::first();
        $producto = Producto::first();

        $response = $this->actingAs($operador, 'sanctum')->postJson('/api/ventas', [
            'cliente_id' => $cliente->id,
            'fecha'      => now()->toDateString(),
            'detalles'   => [
                [
                    'producto_id'     => $producto->id,
                    'cantidad'        => 2,
                    'precio_unitario' => 12000,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total', 24000);
    }

    public function test_user_can_register_compra_and_generate_lotes(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();
        $proveedor = Proveedor::first();
        $producto = Producto::first();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/compras', [
            'proveedor_id' => $proveedor->id,
            'fecha'        => now()->toDateString(),
            'detalles'     => [
                [
                    'producto_id'       => $producto->id,
                    'cantidad'          => 20,
                    'precio_unitario'   => 15000,
                    'fecha_vencimiento' => now()->addDays(90)->toDateString(),
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total', 300000);

        $this->assertDatabaseHas('lotes', [
            'producto_id'      => $producto->id,
            'cantidad_inicial' => 20,
            'cantidad_actual'  => 20,
        ]);
    }

    public function test_user_can_register_venta_with_fifo_discount(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();
        $cliente = Cliente::first();
        $producto = Producto::first();

        // Stock disponible previo
        $stockPrevio = $producto->lotes()->sum('cantidad_actual');
        $this->assertGreaterThan(5, $stockPrevio);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/ventas', [
            'cliente_id' => $cliente->id,
            'fecha'      => now()->toDateString(),
            'detalles'   => [
                [
                    'producto_id'     => $producto->id,
                    'cantidad'        => 5,
                    'precio_unitario' => 25000,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total', 125000);

        $stockPosterior = $producto->lotes()->sum('cantidad_actual');
        $this->assertEquals($stockPrevio - 5, $stockPosterior);
    }

    public function test_venta_fails_when_stock_is_insufficient(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();
        $cliente = Cliente::first();
        $producto = Producto::first();

        $stockDisponible = $producto->lotes()->sum('cantidad_actual');

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/ventas', [
            'cliente_id' => $cliente->id,
            'fecha'      => now()->toDateString(),
            'detalles'   => [
                [
                    'producto_id'     => $producto->id,
                    'cantidad'        => $stockDisponible + 9999, // Supera con creces el stock
                    'precio_unitario' => 10000,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['detalles']);
    }

    public function test_user_can_register_stock_movement(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();
        $lote = Lote::where('cantidad_actual', '>=', 10)->first();
        $stockAntes = $lote->cantidad_actual;

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/movimientos', [
            'lote_id'  => $lote->id,
            'tipo'     => 'merma',
            'cantidad' => 2,
            'motivo'   => 'Fruta golpeada',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.tipo', 'merma')
            ->assertJsonPath('data.cantidad', 2);

        $lote->refresh();
        $this->assertEquals($stockAntes - 2, $lote->cantidad_actual);
    }

    public function test_productos_index_filters_and_includes_stock_total(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/productos?search=Tomate');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'categoria',
                        'stock_minimo',
                        'stock_total',
                        'activo',
                    ],
                ],
            ]);

        $this->assertTrue(collect($response->json('data'))->every(fn ($p) => str_contains($p['nombre'], 'Tomate')));
    }

    public function test_kardex_returns_complete_product_traceability(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();
        $producto = Producto::first();

        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/productos/{$producto->id}/kardex");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'producto',
                'stock_actual',
                'movimientos' => [
                    '*' => ['id', 'lote_id', 'tipo', 'cantidad', 'motivo'],
                ],
            ]);
    }

    public function test_unauthenticated_request_returns_standardized_json_401(): void
    {
        $response = $this->getJson('/api/productos');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'No autenticado. Se requiere un token de acceso válido.');
    }

    public function test_not_found_resource_returns_standardized_json_404(): void
    {
        $admin = User::where('email', 'admin@agrodata.com')->first();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/productos/999999');

        $response->assertStatus(404)
            ->assertJsonPath('message', 'El recurso solicitado no fue encontrado.');
    }
}
