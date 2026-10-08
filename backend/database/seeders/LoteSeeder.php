<?php

namespace Database\Seeders;

use App\Models\Lote;
use App\Models\Movimiento;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoteSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        $hoy = Carbon::today();

        foreach ($productos as $index => $producto) {
            // Configuración de cantidades según el producto para tener variedad (incluyendo casos de bajo stock)
            $isBajoStock = in_array($producto->nombre, ['Lechuga Piratininga', 'Glifosato 480 SL']);

            $cantidades = $isBajoStock
                ? [
                    ['ini' => 5, 'act' => 2],
                    ['ini' => 5, 'act' => 3],
                    ['ini' => 8, 'act' => 4],
                ]
                : [
                    ['ini' => 50, 'act' => 35],
                    ['ini' => 70, 'act' => 60],
                    ['ini' => 100, 'act' => 95],
                ];

            // 3 lotes por producto con vencimientos a 5, 15 y 60 días
            $lotesConfig = [
                [
                    'ingreso'     => $hoy->copy()->subDays(25)->toDateString(),
                    'vencimiento' => $hoy->copy()->addDays(5)->toDateString(),
                    'inicial'     => $cantidades[0]['ini'],
                    'actual'      => $cantidades[0]['act'],
                ],
                [
                    'ingreso'     => $hoy->copy()->subDays(15)->toDateString(),
                    'vencimiento' => $hoy->copy()->addDays(15)->toDateString(),
                    'inicial'     => $cantidades[1]['ini'],
                    'actual'      => $cantidades[1]['act'],
                ],
                [
                    'ingreso'     => $hoy->copy()->subDays(5)->toDateString(),
                    'vencimiento' => $hoy->copy()->addDays(60)->toDateString(),
                    'inicial'     => $cantidades[2]['ini'],
                    'actual'      => $cantidades[2]['act'],
                ],
            ];

            foreach ($lotesConfig as $loteData) {
                $lote = Lote::create([
                    'producto_id'       => $producto->id,
                    'cantidad_inicial'  => $loteData['inicial'],
                    'cantidad_actual'   => $loteData['actual'],
                    'fecha_ingreso'     => $loteData['ingreso'],
                    'fecha_vencimiento' => $loteData['vencimiento'],
                ]);

                // Registrar movimiento inicial de entrada
                Movimiento::create([
                    'lote_id'    => $lote->id,
                    'tipo'       => 'entrada',
                    'cantidad'   => $loteData['inicial'],
                    'motivo'     => 'Carga de stock inicial',
                    'created_at' => $loteData['ingreso'],
                ]);

                // Si se consumió parte del lote, registrar salida o merma
                $consumido = $loteData['inicial'] - $loteData['actual'];
                if ($consumido > 0) {
                    // Si es el primer lote del producto, registrar parte como merma para probar reportes
                    if ($index === 0 && $loteData === $lotesConfig[0]) {
                        Movimiento::create([
                            'lote_id'    => $lote->id,
                            'tipo'       => 'merma',
                            'cantidad'   => 5,
                            'motivo'     => 'Deterioro por maduración',
                            'created_at' => now(),
                        ]);
                        $consumido -= 5;
                    }

                    if ($consumido > 0) {
                        Movimiento::create([
                            'lote_id'    => $lote->id,
                            'tipo'       => 'salida',
                            'cantidad'   => $consumido,
                            'motivo'     => 'Venta / Distribución',
                            'created_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
