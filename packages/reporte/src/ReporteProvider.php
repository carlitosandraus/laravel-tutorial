<?php

namespace Sagicc\Reporte;

use Illuminate\Support\ServiceProvider;

class ReporteProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/Routes/web.php");
    }

    public static function test() {
        return "Hola Mundo";
    }
}
