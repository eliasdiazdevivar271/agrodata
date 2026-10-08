<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre'    => 'Dekalpar S.A.',
                'ruc'       => '80019283-4',
                'telefono'  => '+595 61 570220',
                'direccion' => 'Supercarretera Km 4, Hernandarias, Alto Paraná',
            ],
            [
                'nombre'    => 'Ciabay S.A.',
                'ruc'       => '80023451-9',
                'telefono'  => '+595 61 580100',
                'direccion' => 'Ruta PY02 Km 12, Ciudad del Este',
            ],
            [
                'nombre'    => 'Glymax Paraguay S.A.',
                'ruc'       => '80034125-8',
                'telefono'  => '+595 673 220300',
                'direccion' => 'Santa Rita, Alto Paraná',
            ],
            [
                'nombre'    => 'Agrotec S.A.',
                'ruc'       => '80011223-1',
                'telefono'  => '+595 61 500350',
                'direccion' => 'Km 7 Monday, Ciudad del Este',
            ],
            [
                'nombre'    => 'Somax Agro S.A.',
                'ruc'       => '80045612-5',
                'telefono'  => '+595 673 220550',
                'direccion' => 'Avda. Carlos Antonio López, Santa Rita',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::updateOrCreate(
                ['ruc' => $proveedor['ruc']],
                $proveedor
            );
        }
    }
}
