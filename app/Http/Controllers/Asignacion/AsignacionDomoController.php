<?php

namespace App\Http\Controllers\Asignacion;

use App\AsignacionDomo;
use App\Carnet;
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

            $carnet = Carnet::find($request->carnet_id);
            $carnet->asignado = true;
            $carnet->save();
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

    //imprimir pdf
    public function printAsistencia($asignacion_id,$fecha,$turno = 0){
        $asignacion = AsignacionEmpleado::where('id',$asignacion_id)
                                         ->with('planificacion.buque')
                                         ->firstOrFail();

        $detalle = AsignacionDomo::where([['asignacion_empleado_id',$asignacion_id],['fecha',$fecha]])->with('empleado','cargo','carnet','asistencia_domo')->get();

        $pdf_file = 'pdfs.print_asistencia_domo';
        $detalle_filter = collect();
        $detalle_filter_array = collect();

        foreach ($detalle as $d) {
            $detalle_filter->empleado = $d->empleado->primer_nombre.' '.$d->empleado->segundo_nombre.' '.
                                        $d->empleado->primer_apellido.' '.$d->empleado->segundo_apellido;
            $detalle_filter->dpi = $d->empleado->dpi;
            $detalle_filter->carnet = $d->carnet->codigo;
            $detalle_filter->hora_entrada = null;
            $detalle_filter->hora_salida = null;
            $detalle_filter->cargo = $d->cargo->nombre;
            $detalle_filter->turno = null;
            $detalle_filter->bloqueado = null;
            $detalle_filter->desbloqueado = null;
            $detalle_filter->fecha = $d->fecha;

            if(count($d->asistencia_domo) > 0){
                foreach ($d->asistencia_domo as $ad) {
                    $d_filter = clone $detalle_filter;
                    $d_filter->hora_entrada = $ad->hora_entrada;
                    $d_filter->hora_salida = $ad->hora_salida;
                    $d_filter->turno = $ad->turno;
                    $d_filter->bloqueado = $ad->bloqueado;
                    $d_filter->desbloqueado = $ad->desbloqueado;

                    $detalle_filter_array->push($d_filter);
                }
            }else{
                $detalle_filter_array->push($detalle_filter); 
            }
        }

        if($turno > 0){
            $detalle_filter_array = $detalle_filter_array->where('turno',$turno);
        }
        

        $pdf = \PDF::loadView($pdf_file,['asignacion'=>$asignacion,'detalle'=>$detalle_filter_array])->setPaper('a4', 'landscape');
        
        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('ejemplo.pdf'); 
    }
}
