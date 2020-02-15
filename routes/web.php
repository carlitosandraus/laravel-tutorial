<?php

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
    return view('welcome');
});


Route::get('/web', 'PruebaController@listado');
Route::delete('/web/{id}', 'PruebaController@borrar');
Route::get('/web/{id}', 'PruebaController@obtener');
Route::put('/web/{id}', 'PruebaController@actualizar');
Route::get('/web/crear/dato', 'PruebaController@nuevo');
Route::post('/web', 'PruebaController@crear');
