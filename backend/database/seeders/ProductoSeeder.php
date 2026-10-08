<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre'        => 'Tomate Santa Cruz',
                'categoria'     => 'Hortalizas',
                'unidad_medida' => 'kg',
                'stock_minimo'  => 50,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Lechuga Piratininga',
                'categoria'     => 'Hortalizas',
                'unidad_medida' => 'docena',
                'stock_minimo'  => 30,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Cebolla Colorada',
                'categoria'     => 'Hortalizas',
                'unidad_medida' => 'kg',
                'stock_minimo'  => 40,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Fertilizante NPK 15-15-15',
                'categoria'     => 'Fertilizantes',
                'unidad_medida' => 'bolsa 50kg',
                'stock_minimo'  => 20,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Urea Agrícola 46%',
                'categoria'     => 'Fertilizantes',
                'unidad_medida' => 'bolsa 50kg',
                'stock_minimo'  => 25,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Glifosato 480 SL',
                'categoria'     => 'Agroquímicos',
                'unidad_medida' => 'litro',
                'stock_minimo'  => 15,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Maíz Amarillo en Grano',
                'categoria'     => 'Granos',
                'unidad_medida' => 'kg',
                'stock_minimo'  => 100,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Soja Transgénica RR',
                'categoria'     => 'Granos',
                'unidad_medida' => 'kg',
                'stock_minimo'  => 150,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Zanahoria Primavera',
                'categoria'     => 'Hortalizas',
                'unidad_medida' => 'kg',
                'stock_minimo'  => 35,
                'activo'        => true,
            ],
            [
                'nombre'        => 'Papa Spunta',
                'categoria'     => 'Hortalizas',
                'unidad_medida' => 'kg',
                'stock_minimo'  => 60,
                'activo'        => true,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                ['nombre' => $producto['nombre']],
                $producto
            );
        }
    }
}
