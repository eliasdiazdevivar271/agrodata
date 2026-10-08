<?php

namespace App\Providers;

use App\Models\Compra;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Venta;
use App\Policies\CompraPolicy;
use App\Policies\LotePolicy;
use App\Policies\ProductoPolicy;
use App\Policies\VentaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Producto::class, ProductoPolicy::class);
        Gate::policy(Lote::class, LotePolicy::class);
        Gate::policy(Venta::class, VentaPolicy::class);
        Gate::policy(Compra::class, CompraPolicy::class);
    }
}
