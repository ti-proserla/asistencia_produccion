<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Exports\HorasSemanaTrabajadorExport;
use Maatwebsite\Excel\Facades\Excel;

header("Access-Control-Allow-Origin: *");

Route::resource('operador', 'OperadorController');
Route::post('operador/{id}/estado','OperadorController@estado')->name('operador.estado');

Route::resource('area', 'AreaController');
Route::post('area/{id}/estado','AreaController@estado')->name('area.estado');

Route::resource('labor', 'LaborController');
Route::post('labor/{id}/estado','LaborController@estado')->name('labor.estado');
Route::resource('proceso', 'ProcesoController');
Route::post('proceso/{id}/estado','ProcesoController@estado')->name('proceso.estado');

Route::resource('turno', 'TurnoController');

Route::post('marcacion', 'MarcadorController@store')->name('marcacion.store');
Route::post('tareo', 'TareoController@store')->name('tareo.store');

Route::get('reporte-turno', 'ReporteController@turno');
Route::get('reporte-pendientes', 'ReporteController@pendientes');

Route::post('conteo','ConteoController@nuevo');
Route::get('conteo','ConteoController@reporte');
Route::get('conteoOperario','ConteoController@reporteOperario');


Route::get('/horas-semana/{anio}/{semana}', function ($anio,$semana) {
    return Excel::download(new HorasSemanaTrabajadorExport($anio,$semana), "horas-semana-$anio-$semana.xlsx");
});