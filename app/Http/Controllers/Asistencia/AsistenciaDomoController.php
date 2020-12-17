<?php

namespace App\Http\Controllers\Asistencia;

use App\AsistenciaDomo;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;

class AsistenciaDomoController extends ApiController
{
     public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $asistenciaDomos = AsistenciaDomo::all();
        return $this->showAll($asistenciaDomos);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'asignacion_domo_id'=>'required|integer',
            'turno'
        ];

        $this->validate($request,$rules);
        $data = $request->all();

        if($request->bloqueado){
            $asistenciaDomo = AsistenciaDomo::create($data);

            return $this->errorResponse('no se a podido registrar la asistencia de entrada, por llegar tarde, el empleado a sido bloqueado, si desea desbloquear el empleado comuniquese con el administrador',422);
        }else{
            $today = Carbon::now('America/Guatemala');

            $data['hora_entrada'] = $today;

            $asistenciaDomo = AsistenciaDomo::create($data);

            return $this->showOne($asistenciaDomo,201,'insert');
        }

        
    }

    public function update(Request $request, AsistenciaDomo $asistencia_domo)
    {
        $rules = [
            'asignacion_domo_id'=>'required|integer'
        ];

        $this->validate($request,$rules);

        $today = Carbon::now('America/Guatemala');

        if($request->salida)
        {
            $asistencia_domo->hora_salida = $today;
        }else{
            $asistencia_domo->hora_entrada = $today;
        }

        if(!$asistencia_domo->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $asistencia_domo->save();

        return $this->showOne($asistencia_domo,201,'update');

    }

    public function desbloquear($id, Request $request){

        $rules = [
            'razon_desbloqueo' => 'required'
        ];

        $this->validate($request,$rules);

        $data = AsistenciaDomo::find($id);
        $data->razon_desbloqueo= $request->razon_desbloqueo;
        $data->desbloqueado = true;
        $data->save();

        return $this->showOne($data,201,'update');
    } 

    /**
     */
    public function show(AsistenciaDomo $asistencia_domo)
    {
        return $this->showOne($asistencia_domo,200,'select');
    }
}
