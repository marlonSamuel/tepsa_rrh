<?php

namespace App\Http\Controllers\Asistencia;

use Illuminate\Http\Request;
use App\AsistenciaTurnoBodega;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class AsistenciaTurnoBodegaController extends ApiController
{
    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
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
            'hora_entrada'=>'required',
            'bodega'=>'required'
        ];

        $this->validate($request,$rules);
        $data = $request->all();

        $asistenciaTurnoBodega = AsistenciaTurnoBodega::create($data);

        return $this->showOne($asistenciaTurnoBodega,201,'insert');
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
            'hora_entrada'=>'required',
            'bodega'=>'required',
            'hora_salida' => 'required'
        ];


        $this->validate($request,$rules);

        $asistencia_turno_bodega->hora_salida = $request->hora_salida;
        $asistencia_turno_bodega->bodega = $request->bodega;
        $asistencia_turno_bodega->cargo_turno_id = $request->cargo_turno_id;

        if(!$asistencia_turno_bodega->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $asistencia_turno_bodega->save();

        return $this->showOne($asistencia_turno_bodega,201,'update');

    }

    /**
     */
    public function destroy(AsistenciaTurnoBodega $asistencia_turno_bodega)
    {
        $asistencia_turno_bodega->delete();

        return $this->showOne($asistencia_turno_bodega,201,'delete');
    }
}
