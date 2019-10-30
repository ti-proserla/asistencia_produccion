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

/**
 * Vistas
 */
Route::get('/', function () {
    return view('welcome');
});
Route::get('capturador', function () {
    return view('capturador');    
});
Route::get('reporte', function () {
    return view('reporte');    
});
Route::get('scanner', function () {
    return view('scanner');    
});
Route::get('refresh', function () {
    return view('scanner');    
});
Route::get('refreshOperario', function () {
    return view('scanner');    
});
/**
 * Operaciones
 */
Route::get('linea', 'LineaController@index');
Route::get('producto/nuevo','ProductoController@nuevo');
Route::get('producto/editar','ProductoController@editar');
Route::get('producto/eliminar','ProductoController@eliminar');
