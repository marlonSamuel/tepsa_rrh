<?php

namespace App\Http\Controllers\Asignacion;

use App\AsignacionDomo;
use App\AsignacionEmpleado;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class AsignacionDomoController extends ApiController
{
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $detalle = AsignacionDomo::with('asignacion','carnet','empleado','cargo')->get();
        return $this->showAll($detalle);
    }

    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'asignacion_empleado_id' => 'required|integer',
            'carnet_id'=>'required',
            'empleado_id'=>'required',
            'fecha'=>'required',
            'cargo_id' => 'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();
            $this->validate($request,$rules);

            $data = $request->all();

            $asignacion = AsignacionDomo::create($data);

            /*$data_d = new DetalleAsignacionEmpleado;
            $data_d->turno_id = $request->turno_id;
            $data_d->asignacion_empleado_id = $asignacion->id;
            $data_d->carnet_id = $request->carnet_id;
            $data_d->empleado_id = $request->empleado_id;
            $data_d->fecha = $request->fecha;

            $data_d->save();

            $carnet = Carnet::find($request->carnet_id);
            $carnet->asignado = true;
            $carnet->save();*/
        $db->commit();

        return $this->showOne($asignacion,201,'insert');
    }

    //retornar asignacion de empleado, codigo turno y fecha
    public function showAsign($codigo,$fecha){

        $carnet = Carnet::where('codigo',$codigo)->firstOrFail();

        $asignacion = AsignacionDomo::where([['carnet_id',$carnet->id],['fecha',$fecha]])->with('empleado','asistencia_domo','asignacion.planificacion.buque','cargo')->first();
        
        if(is_null($asignacion)){
            return $this->errorResponse('no se encontró asignación de este empleado con el codigo '.$codigo.' y fecha solicitada', 421);
        }

        return $this->showOne($asignacion);
    }

    //obtener data
    public function getByDate($fecha)
    {
        $asignacion = AsignacionDomo::where([['fecha',$fecha]])->with('empleado','asistencia_domo','asignacion.planificacion.buque','cargo')->get();

        //$asignacion = $asignacion->where('asistencia_domo','!=',[])->values();

        return $asignacion;
    }

    /**
     */
    public function destroy(AsignacionDomo $asignacion_domo)
    {
        $asignacion_domo->delete();
        return $this->showOne($asignacion_domo,201,'delete');
    }

    //imprimir pdf
    public function print($id,$fecha)
    {
        $asignacion = AsignacionEmpleado::where('id',$id)
                                         ->with('planificacion.buque')
                                         ->firstOrFail();

        $detalle = AsignacionDomo::where([['asignacion_empleado_id',$id],['fecha',$fecha]])->with('empleado','cargo','carnet')->get();


        $pdf = \PDF::loadView('pdfs.print_asignacion_domo',['asignacion'=>$asignacion,'detalle'=>$detalle])->setPaper('a4', 'landscape');


        
        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('ejemplo.pdf');
        
    }
}
