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
Route::resource('planilla', 'PlanillaController');
Route::post('planilla/{id}/estado','PlanillaController@estado')->name('planilla.estado');

Route::get('fundo/proceso','FundoController@proceso')->name('fundo.proceso');
Route::resource('fundo', 'FundoController')->middleware('auth.token');
Route::get('area/labor','AreaController@labor')->name('area.labor');
Route::resource('area', 'AreaController');
Route::post('area/{id}/estado','AreaController@estado')->name('area.estado');

Route::resource('labor', 'LaborController');
Route::post('labor/{id}/estado','LaborController@estado')->name('labor.estado');

Route::resource('proceso', 'ProcesoController')->middleware('auth.token');

Route::post('proceso/{id}/estado','ProcesoController@estado')->name('proceso.estado');
Route::resource('linea', 'LineaController');
Route::post('linea/{id}/estado','LineaController@estado')->name('linea.estado');

Route::resource('turno', 'TurnoController');

Route::post('marcador2','MarcadorController@store2')->name('marcador.store2');
Route::resource('marcador', 'MarcadorController');

Route::post('tareo', 'TareoController@store')->name('tareo.store');

Route::get('reporte-turno', 'ReporteController@turno');
Route::get('reporte-semana', 'ReporteController@semana');
Route::get('reporte-pendientes', 'ReporteController@pendientes');
Route::get('reporte/pendientes-regularizar', 'ReporteController@pendientesRegularizar');
Route::get('reporte-marcas', 'ReporteController@marcas');

Route::post('conteo','ConteoController@nuevo');
Route::get('conteo','ConteoController@reporte');
Route::get('conteoOperario','ConteoController@reporteOperario');


Route::get('jne/dni/{dni}', 'OperadorController@jne');

Route::get('/horas-semana/{anio}/{semana}', function ($anio,$semana) {
    return Excel::download(new HorasSemanaTrabajadorExport($anio,$semana,0), "horas-semana-$anio-$semana.xlsx");
});

Route::get('/horas-semana/{anio}/{semana}/{planilla_id}', function ($anio,$semana,$planilla_id) {
    return Excel::download(new HorasSemanaTrabajadorExport($anio,$semana,$planilla_id), "horas-semana-$anio-$semana.xlsx");
});
Route::get('/marcas-tuno/{turno_id}', function ($turno_id) {
    return Excel::download(new MarcasTurnoTrabajadorExport($turno_id), "marcas-turno-$turno_id.xlsx");
});

Route::get('/producto', function () {
    return response()->json([
        [
            "id"    =>  1,
            "nombre"=>  "UVA"
        ],
        [
            "id"    =>  2,
            "nombre"=>  "MANGO"
        ],
        [
            "id"    =>  3,
            "nombre"=>  "LIMON"
        ]
    ]);
});

/**
 * Para trabajadores
 */
Route::get('/consulta/marcas', function ($id) {
    
});

Route::get('sincronizar/proceso',"SincronizarController@proceso");
Route::get('sincronizar/labor',"SincronizarController@labor");
Route::get('sincronizar/area',"SincronizarController@area");
Route::resource('configuracion',"ConfiguracionController");