<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre'    => 'Agropecuaria San Isidro S.A.',
                'ruc'       => '80012456-7',
                'telefono'  => '+595 981 234567',
                'direccion' => 'Ruta PY02 Km 180, Caaguazú',
            ],
            [
                'nombre'    => 'Juan Carlos Benítez',
                'ruc'       => '3456789-1',
                'telefono'  => '+595 971 890123',
                'direccion' => 'Avda. San Blas, Ciudad del Este',
            ],
            [
                'nombre'    => 'María Elena Villalba',
                'ruc'       => '2890123-4',
                'telefono'  => '+595 983 456789',
                'direccion' => 'Barrio Santa Ana, Coronel Oviedo',
            ],
            [
                'nombre'    => 'Cooperativa Colonias Unidas Ltda.',
                'ruc'       => '80004561-2',
                'telefono'  => '+595 71 202234',
                'direccion' => 'Avda. Gaspar Rodríguez de Francia, Obligado, Itapúa',
            ],
            [
                'nombre'    => 'Ganadera y Agrícola Chaco Central S.R.L.',
                'ruc'       => '80056789-0',
                'telefono'  => '+595 991 334455',
                'direccion' => 'Loma Plata, Boquerón',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(
                ['ruc' => $cliente['ruc']],
                $cliente
            );
        }
    }
}
