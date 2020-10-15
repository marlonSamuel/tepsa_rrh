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
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

Route::name('me')->get('auth/me', 'Usuario\AuthController@user');
Route::name('cambiar_contraseña')->post('usuarios_change_password', 'Usuario\UsuarioController@changePassword');


#=======================EMPLEADOS=========================================================#
Route::resource('empleados', 'Empleado\EmpleadoController', ['except' => ['create', 'edit']]);

#=======================CARGOS=========================================================#
Route::resource('cargos', 'Cargo\CargoController', ['except' => ['create', 'edit']]);

#=======================TURNOS=========================================================#
Route::resource('turnos', 'Turno\TurnoController', ['except' => ['create', 'edit']]);

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

Route::name('dataTurn')->get('asignacion_empleados/{id}/{turno_id}/{fecha}', 'Asignacion\AsignacionEmpleadoController@getDataTurn');

Route::resource('detalle_asignacion_empleados', 'Asignacion\DetalleAsignacionEmpleadoController', ['except' => ['create', 'edit']]);