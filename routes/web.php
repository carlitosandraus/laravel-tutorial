<?php
use Faker\Factory;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
/*    for( $i=0;$i<50;$i++ ) {
        $faker = Factory::create(); 
        $a = new App\Models\Prueba;
        $a->nombre= $faker->name;
        $a->edad= $faker->randomNumber(2);
        $a->save();    
}*/
    return view('welcome');
});


Route::get('/web', 'PruebaController@listado');
Route::delete('/web/{id}', 'PruebaController@borrar');
Route::get('/web/{id}', 'PruebaController@obtener');
Route::put('/web/{id}', 'PruebaController@actualizar');
Route::get('/web/crear/dato', 'PruebaController@nuevo');
Route::post('/web', 'PruebaController@crear');
