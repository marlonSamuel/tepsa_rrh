<?php

namespace App\Http\Controllers\Asistencia;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\AsistenciaTurnoBodega;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class AsistenciaTurnoBodegaController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $asistenciaTurnoBodegas = AsistenciaTurnoBodega::all();
        return $this->showAll($asistenciaTurnoBodegas);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'detalle_asignacion_empleado_id'=>'required|integer',
            'cargo_turno_id'=>'required|integer',
            'bodega'=>'required',
            'bloqueado' => 'required'
        ];

        $this->validate($request,$rules);
        $today = Carbon::now('America/Guatemala');
        $data = $request->all();

        if($request->bloqueado){
            $asistenciaTurnoBodega = AsistenciaTurnoBodega::create($data);

            return $this->errorResponse('no se a podido registrar la entrada, por entrada tarde, el empleado a sido bloqueado, si desea desbloquear el empleado comuniquese con el administrador',422);
        }else{
             $data['hora_entrada'] = $today; 
            $asistenciaTurnoBodega = AsistenciaTurnoBodega::create($data);

            return $this->showOne($asistenciaTurnoBodega,201,'insert');
        }

       
    }

    /**
     */
    public function show(AsistenciaTurnoBodega $asistencia_turno_bodega)
    {
        return $this->showOne($asistencia_turno_bodega,200,'select');
    }

    /**
     */
    public function update(Request $request, AsistenciaTurnoBodega $asistencia_turno_bodega)
    {
        $rules = [
            'detalle_asignacion_empleado_id'=>'required|integer',
            'cargo_turno_id'=>'required|integer',
            'bodega'=>'required'
        ];


        $this->validate($request,$rules);

        if($request->salida)
        {
            $today = Carbon::now('America/Guatemala');
            $asistencia_turno_bodega->hora_salida = $today;
        }else{
            $today = Carbon::now('America/Guatemala');
            $asistencia_turno_bodega->hora_entrada = $today;
        }
        
        $asistencia_turno_bodega->bodega = $request->bodega;
        $asistencia_turno_bodega->cargo_turno_id = $request->cargo_turno_id;
        $asistencia_turno_bodega->observaciones = $request->observaciones;

        if(!$asistencia_turno_bodega->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $asistencia_turno_bodega->save();

        return $this->showOne($asistencia_turno_bodega,201,'update');

    }

    public function desbloquear($id, Request $request){
        $data = AsistenciaTurnoBodega::find($id);

        $data->razon_desbloqueo= $request->razon_desbloqueo;
        $data->desbloqueado = true;

        $data->save();
        return $this->showOne($data,201,'update');
    }   

    /**
     */
    public function destroy(AsistenciaTurnoBodega $asistencia_turno_bodega)
    {
        $asistencia_turno_bodega->delete();

        return $this->showOne($asistencia_turno_bodega,201,'delete');
    }
}
