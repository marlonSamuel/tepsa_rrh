<?php

namespace App\Http\Controllers\Asistencia;

use App\AsistenciaAlmuerzo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class AsisenciaAlmuerzoController extends ApiController
{
     public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $asistenciaAlmuerzos = asistenciaAlmuerzo::all();
        return $this->showAll($asistenciaAlmuerzos);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'detalle_asignacion_empleado_id'=>'required|integer'
        ];

        $this->validate($request,$rules);
        $data = $request->all();

        $asistenciaAlmuerzo = asistenciaAlmuerzo::create($data);

        return $this->showOne($asistenciaAlmuerzo,201,'insert');
    }

    /**
     */
    public function show(asistenciaAlmuerzo $asistencia_almuerzo)
    {
        return $this->showOne($asistencia_almuerzo,200,'select');
    }

    /**
     */
    public function update(Request $request, AsistenciaAlmuerzo $asistencia_almuerzo)
    {
       
    }

    /**
     */
    public function destroy(AsistenciaAlmuerzo $asistencia_almuerzo)
    {
        $asistencia_almuerzo->delete();

        return $this->showOne($asistencia_almuerzo,201,'delete');
    }
}
