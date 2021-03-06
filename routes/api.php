<?php

use Illuminate\Http\Request;

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


Route::name('login')->post('auth/login', 'Usuario\AuthController@login');
Route::name('logout')->get('auth/logout', 'Usuario\AuthController@logout');

Route::resource('usuarios', 'Usuario\UsuarioController', ['except' => ['create', 'edit']]);
Route::resource('roles', 'Rol\RolController', ['except' => ['create', 'edit']]);
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

Route::name('me')->get('auth/me', 'Usuario\AuthController@user');
Route::name('cambiar_contraseña')->post('usuarios_change_password', 'Usuario\UsuarioController@changePassword');


#=======================EMPLEADOS=========================================================#
Route::resource('empleados', 'Empleado\EmpleadoController', ['except' => ['create', 'edit']]);
Route::resource('empleado_prestaciones', 'Empleado\EmpleadoPrestacionController', ['except' => ['create', 'edit']]);
Route::name('disabled_empleado')->post('empleados_disabled/{idEmpleado}', 'Empleado\EmpleadoController@disabledEmpleado');
Route::name('get_foto')->get('empleados_foto/{idCarnet}', 'Empleado\EmpleadoController@empleado_carnet');

#=======================CARGOS=========================================================#
Route::resource('cargos', 'Cargo\CargoController', ['except' => ['create', 'edit']]);
Route::name('disabled_cargo')->post('cargos_disabled/{idCargo}', 'Cargo\CargoController@disable');
Route::resource('cargos.turnos', 'Cargo\CargoTurnoController', ['except' => ['create', 'edit']]);


#=======================TURNOS=========================================================#
Route::resource('turnos', 'Turno\TurnoController', ['except' => ['create', 'edit']]);
Route::resource('turnos.cargos', 'Turno\TurnoCargoController', ['except' => ['create', 'edit']]);

#=======================CARNETS=========================================================#
Route::resource('carnets', 'Carnet\CarnetController', ['except' => ['create', 'edit']]);


#=======================PRESTACIONES=========================================================#
Route::resource('prestacions', 'Prestacion\PrestacionController', ['except' => ['create', 'edit']]);

#=======================PLANO ESTIBAS=========================================================#
Route::resource('planificaciones', 'PlanoEstiba\PlanoEstibaController', ['except' => ['create', 'edit']]);
Route::name('search_planificacion')->get('planificaciones_search/{date}/{buque_id}', 'PlanoEstiba\PlanoEstibaController@search');

#=======================BUQUES=========================================================#
Route::resource('buques', 'Buque\BuqueController', ['except' => ['create', 'edit']]);

#=======================ASIGNACIONES=========================================================#
Route::resource('asignacion_empleados', 'Asignacion\AsignacionEmpleadoController', ['except' => ['create', 'edit']]);
Route::name('releaseCards')->get('asignacion_empleados_show_one/{id}', 'Asignacion\AsignacionEmpleadoController@showOneRow');

Route::name('asignacion_empleados_asignacion')->get('asignacion_empleados_asignacion/{date}/{buque}', 'Asignacion\AsignacionEmpleadoController@asignacion');
Route::name('asignacion_empleados_domos')->get('asignacion_empleados_domos/{id}', 'Asignacion\AsignacionEmpleadoController@showAsignacionDomo');


Route::name('releaseCards')->put('asignacion_empleados_release/{id}', 'Asignacion\AsignacionEmpleadoController@releaseCards');

Route::name('dataTurn')->get('asignacion_empleados/{id}/{turno_id}/{fecha}', 'Asignacion\AsignacionEmpleadoController@getDataTurn');
Route::name('getByEmpleado')->get('asignacion_empleados_get/{id}/{empleado_id}', 'Asignacion\AsignacionEmpleadoController@getByEmpleadoAsignacion');

Route::resource('detalle_asignacion_empleados', 'Asignacion\DetalleAsignacionEmpleadoController', ['except' => ['create', 'edit']]);
Route::name('showAsign')->get('detalle_asignacion_empleados/{codigo}/{fecha}/{turno_id}/{turno_id_2}', 'Asignacion\DetalleAsignacionEmpleadoController@showAsign');
Route::name('showTurnDate')->get('detalle_asignacion_empleados/{fecha}/{turno_id}', 'Asignacion\DetalleAsignacionEmpleadoController@showTurnDate');

Route::name('getByEmpleadoAsistencia')->get('detalle_asignacion_empleados_asistencia/{id}/{empleado_id}', 'Asignacion\DetalleAsignacionEmpleadoController@getByEmpleado');

Route::resource('asignacion_domos', 'Asignacion\AsignacionDomoController', ['except' => ['create', 'edit']]);
Route::name('showAsign')->get('asignacion_domos/{codigo}/{fecha}', 'Asignacion\AsignacionDomoController@showAsign');
Route::name('getByEmpleado')->get('asignacion_domos_get/{id}/{empleado_id}', 'Asignacion\AsignacionEmpleadoController@getByEmpleadoAsignacion');



Route::name('print_asignacion')->get('asignacion_empleados_print/{id}/{turno_id}/{fecha}/{empleado_id?}', 'Asignacion\AsignacionEmpleadoController@print');

Route::name('print_asignacion_domo')->get('asignacion_domos_print/{id}/{fecha}', 'Asignacion\AsignacionDomoController@print');

Route::name('asignacion_domos_print_asistencia')->get('asignacion_domos_print_asistencia/{id}/{fecha}/{turno?}', 'Asignacion\AsignacionDomoController@printAsistencia');

Route::name('print_detalle_asignacion')->get('detalle_asignacion_empleados_print/{asignacion_id}/{turno_id}/{fecha}/{a?}/{bodega?}', 'Asignacion\DetalleAsignacionEmpleadoController@print');

Route::name('print_almuerzo_detalle_asignacion')->get('detalle_asignacion_empleados_print_almuerzo/{asignacion_id}/{turno_id}/{fecha}', 'Asignacion\DetalleAsignacionEmpleadoController@printAlmuerzo');


Route::resource('detalle_asignacion_empleados', 'Asignacion\DetalleAsignacionEmpleadoController', ['except' => ['create', 'edit']]);

#=======================ASISTENCIAS=========================================================#
Route::resource('asistencia_turno_bodegas', 'Asistencia\AsistenciaTurnoBodegaController', ['except' => ['create', 'edit']]);

Route::name('asistencia_turno_bodegas_desbloquear')->put('asistencia_turno_bodegas_desbloquear/{id}', 'Asistencia\AsistenciaTurnoBodegaController@desbloquear');

Route::resource('asistencia_almuerzos', 'Asistencia\AsistenciaAlmuerzoController', ['except' => ['create', 'edit']]);

Route::resource('asistencia_domos', 'Asistencia\AsistenciaDomoController', ['except' => ['create', 'edit']]);
Route::name('asistencia_domos_desbloquear')->put('asistencia_domos_desbloquear/{id}', 'Asistencia\AsistenciaDomoController@desbloquear');

#=======================PAGOS=========================================================#
Route::resource('planilla_eventuals', 'Pago\PlanillaEventualController', ['except' => ['create', 'edit']]);
Route::name('planilla_eventuals_info')->get('planilla_eventuals_info/{id}/{option}', 'Pago\PlanillaEventualController@info');
Route::resource('pago_empleado_eventuals', 'Pago\PagoEmpleadoEventualController', ['except' => ['create', 'edit']]);
Route::resource('pago_empleado_fijos', 'Pago\PagoEmpleadoFijoController', ['except' => ['create', 'edit']]);
Route::name('pago_empleado_fijos_info')->get('pago_empleado_fijos_info/{id}/{option}', 'Pago\PagoEmpleadoFijoController@info');
Route::name('pago_empleado_fijos_export')->get('pago_empleado_fijos_export/{id}', 'Pago\PagoEmpleadoFijoController@export');
Route::name('pago_empleado_fijos_mes')->get('pago_empleado_fijos_mes/', 'Pago\PagoEmpleadoFijoController@getMes');
Route::name('pago_empleado_fijos_quincena')->get('pago_empleado_fijos_quincena/{id}', 'Pago\PagoEmpleadoFijoController@getQuincenas');
Route::name('pago_empleado_fijos_planilla')->get('pago_empleado_fijos_planilla/{id}', 'Pago\PagoEmpleadoFijoController@getPlanilla');
Route::name('pago_empleado_fijos_print_boleta')->get('pago_empleado_fijos_print_boleta/{quincena_id}/{id?}', 'Pago\PagoEmpleadoFijoController@print');

Route::name('pago_empleado_eventuals_print_boleta')->get('pago_empleado_eventuals_print_boleta/{planificacion_id}/{id?}', 'Pago\PagoEmpleadoEventualController@print');

Route::name('planilla_eventuals_export')->get('planilla_eventuals_export/{id}', 'Pago\PlanillaEventualController@export');

Route::resource('pago_domo_empleados', 'Pago\PlanillaEventualDomoController', ['except' => ['create', 'edit']]);
Route::name('pago_domo_empleados_info')->get('pago_domo_empleados_info/{id}/{option}', 'Pago\PlanillaEventualDomoController@info');
Route::name('pago_domo_empleados_export')->get('pago_domo_empleados_export/{id}', 'Pago\PlanillaEventualDomoController@export');
Route::name('pago_domo_empleados_print_boleta')->get('pago_domo_empleados_print_boleta/{planificacion_id}/{id?}', 'Pago\PlanillaEventualDomoController@print');
/*
Route::name('planilla_eventuals_domo')->get('planilla_eventuals_domo/{id}/{option}', 'Pago\PlanillaEventualController@pago_domo');*/

#=======================DASHBOARD=========================================================#
Route::resource('dashboard', 'Dashboard\DashboardController', ['except' => ['create', 'edit']]);
Route::name('dashboard_group_by_planilla')->get('dashboard_group_by_planilla', 'Dashboard\DashboardController@groupByPlanilla');
Route::name('turnos_start_quincena')->get('turnos_start_quincena', 'Turno\TurnoController@start_quincena');