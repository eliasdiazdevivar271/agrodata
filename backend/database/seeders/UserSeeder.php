<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@agrodata.com'],
            [
                'name'     => 'Administrador AgroData',
                'password' => Hash::make('password'),
                'rol'      => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'operador@agrodata.com'],
            [
                'name'     => 'Operador AgroData',
                'password' => Hash::make('password'),
                'rol'      => 'operador',
            ]
        );
    }
}
