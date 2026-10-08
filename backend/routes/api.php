<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\LoteController;
use App\Http\Controllers\Api\MovimientoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas de Autenticación
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Trazabilidad y Kardex
    Route::get('/productos/{producto}/kardex', [ProductoController::class, 'kardex']);
    Route::get('/lotes/{lote}/movimientos', [LoteController::class, 'movimientos']);

    // Recursos API accesibles para operadores y administradores
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);

    Route::apiResource('movimientos', MovimientoController::class)->only(['index', 'store', 'show']);
    Route::apiResource('ventas', VentaController::class)->only(['index', 'store', 'show']);

    // Rutas exclusivas para administradores (Compras y Reportes estratégicos/financieros)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin-only', function () {
            return response()->json([
                'message' => 'Acceso concedido a panel de administración.',
            ]);
        });

        Route::apiResource('compras', CompraController::class)->only(['index', 'store', 'show']);

        Route::prefix('reportes')->group(function () {
            Route::get('/dashboard', [ReporteController::class, 'index']);
            Route::get('/por-vencer', [ReporteController::class, 'porVencer']);
            Route::get('/mermas', [ReporteController::class, 'mermas']);
            Route::get('/bajo-stock', [ReporteController::class, 'bajoStock']);
            Route::get('/ventas-mes', [ReporteController::class, 'ventasMes']);
        });
    });
});
