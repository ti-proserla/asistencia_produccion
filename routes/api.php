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
use App\Exports\MarcasTurnoTrabajadorExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\Operador;


header("Access-Control-Allow-Origin: *");

Route::resource('cuenta', 'CuentaController');
Route::post('login','CuentaController@login')->name('cuenta.login');
Route::resource('operador', 'OperadorController');
Route::post('operador/{id}/estado','OperadorController@estado')->name('operador.estado');

Route::get('area/labor','AreaController@labor')->name('area.labor');
Route::resource('area', 'AreaController');
Route::post('area/{id}/estado','AreaController@estado')->name('area.estado');

Route::resource('labor', 'LaborController');
Route::post('labor/{id}/estado','LaborController@estado')->name('labor.estado');
Route::resource('proceso', 'ProcesoController');
Route::post('proceso/{id}/estado','ProcesoController@estado')->name('proceso.estado');
Route::resource('linea', 'LineaController');
Route::post('linea/{id}/estado','LineaController@estado')->name('linea.estado');

Route::resource('turno', 'TurnoController');

Route::resource('marcador', 'MarcadorController');
Route::post('tareo', 'TareoController@store')->name('tareo.store');

Route::get('reporte-turno', 'ReporteController@turno');
Route::get('reporte-turno2', 'ReporteController@turno2');
Route::get('reporte-pendientes', 'ReporteController@pendientes');
Route::get('reporte-marcas', 'ReporteController@marcas');

Route::post('conteo','ConteoController@nuevo');
Route::get('conteo','ConteoController@reporte');
Route::get('conteoOperario','ConteoController@reporteOperario');


Route::get('jne/dni/{dni}', 'OperadorController@jne');

Route::get('/horas-semana/{anio}/{semana}', function ($anio,$semana) {
    return Excel::download(new HorasSemanaTrabajadorExport($anio,$semana), "horas-semana-$anio-$semana.xlsx");
});
Route::get('/marcas-tuno/{turno_id}', function ($turno_id) {
    return Excel::download(new MarcasTurnoTrabajadorExport($turno_id), "marcas-turno-$turno_id.xlsx");
});