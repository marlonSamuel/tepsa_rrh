<?php

namespace App\Http\Controllers\Asignacion;

use App\Carnet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DetalleAsignacionEmpleado;
use App\AsignacionEmpleado;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\DB;

class DetalleAsignacionEmpleadoController extends ApiController
{
    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $detalle = DetalleAsignacionEmpleado::with('asignacion','turno','carnet','empleado')->get();
        return $this->showAll($detalle);
    }

    //retornar asignacion de empleado, codigo turno y fecha
    public function showAsign($codigo,$fecha,$turno_id,$turno_id_2){

        $carnet = Carnet::where('codigo',$codigo)->firstOrFail();

        $asignacion = DetalleAsignacionEmpleado::where([['carnet_id',$carnet->id],['fecha',$fecha],['turno_id',$turno_id]])->with('empleado','asistencia_turno.cargo_turno.cargo','asignacion.planificacion.buque','turno','asistencia_turno.cargo_turno.turno')->first();

        if(is_null($asignacion)){
            $asignacion = DetalleAsignacionEmpleado::where([['carnet_id',$carnet->id],['fecha',$fecha],['turno_id',$turno_id_2]])->with('empleado','asistencia_turno.cargo_turno.cargo','asignacion.planificacion.buque','turno','asistencia_turno.cargo_turno.turno')->first();
        }
        
        if(is_null($asignacion)){
            return $this->errorResponse('no se encontró asignación de este empleado con el codigo '.$codigo.' fecha y turno correspondiente', 421);
        }

        return $this->showOne($asignacion);
    }

    //obtener data
    public function getTurnDate($fecha,$turno_id)
    {
        $asignacion = DetalleAsignacionEmpleado::where([['fecha',$fecha],['turno_id',$turno_id]])->with('empleado','asistencia_turno.cargo_turno.cargo','asignacion.planificacion.buque','turno','asistencia_turno.cargo_turno.turno')->get();

        $asignacion = $asignacion->where('asistencia_turno','!=',null)->values();

        /*$response = collect();
        foreach ($asignacion as $a) {
            if($a->asistencia_turno->cargo_turno->turno_id == $turno_id){
                $response->push($a);
            }
        }*/
        return $asignacion;
    }

    //retornar asignaciones por fecha y turno
    public function showTurnDate($fecha,$turno_id){
        $asignacion = $this->getTurnDate($fecha,$turno_id);
        return $this->showAll($asignacion);
    }

    //imprimir pdf
    public function print($asignacion_id,$turno_id,$fecha,$a,$bodega = 0){
        $asignacion = AsignacionEmpleado::where('id',$asignacion_id)
                                         ->with('planificacion.buque')
                                         ->firstOrFail();

        $detalle = DetalleAsignacionEmpleado::where([['asignacion_empleado_id',$asignacion_id],['turno_id',$turno_id],['fecha',$fecha]])->with('empleado','turno','carnet','asistencia_turno.cargo_turno.cargo','asistencia_almuerzo')->get();

        $pdf_file = 'pdfs.print_asistencia_turno';

        if($a=="true"){
            $pdf_file = 'pdfs.print_asistencia_almuerzo';
            foreach ($detalle as $d) {
                foreach ($d->asistencia_almuerzo as $a) {
                    if($a->tipo_alimento == 'D'){
                        $a->alimento = 'Desayuno';
                    }else if($a->tipo_alimento == 'A'){
                        $a->alimento == 'Almuerzo';
                    }else if($a->tipo_alimento == 'C'){
                        $a->alimento = 'Cena';
                    }else{
                        $a->alimento = 'Refacción';
                    }
                };
                
            }
            
        }

        $pdf = \PDF::loadView($pdf_file,['asignacion'=>$asignacion,'detalle'=>$detalle])->setPaper('a4', 'landscape');
        
        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('ejemplo.pdf'); 
    }

    //imprimir pdf
    public function printAlmuerzo($asignacion_id,$turno_id,$fecha){
        $asignacion = AsignacionEmpleado::where('id',$asignacion_id)
                                         ->with('planificacion.buque')
                                         ->firstOrFail();

        $detalle = DetalleAsignacionEmpleado::where([['asignacion_empleado_id',$asignacion_id],['turno_id',$turno_id],['fecha',$fecha]])->with('empleado','turno','carnet','asistencia_turno.cargo_turno.cargo','asistencia_almuerzo')->get();

        $pdf = \PDF::loadView('pdfs.print_asistencia_almuerzo',['asignacion'=>$asignacion,'detalle'=>$detalle])->setPaper('a4', 'landscape');
        
        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('ejemplo.pdf'); 
    }

    /**
     */
    public function destroy(DetalleAsignacionEmpleado $detalle_asignacion_empleado)
    {
        DB::beginTransaction();
        $carnet = Carnet::find($detalle_asignacion_empleado->carnet_id);
        $carnet->asignado = false;
        $carnet->save();
        $detalle_asignacion_empleado->delete();
        DB::commit();
        return $this->showOne($detalle_asignacion_empleado,201,'delete');
    }


        //obtener asistencias
    public function getByEmpleado($asignacion_id,$empleado_id)
    {
        $asistencias = DetalleAsignacionEmpleado::where([['asignacion_empleado_id',$asignacion_id],['empleado_id',$empleado_id]])->with('asistencia_almuerzo')->get()->pluck('asistencia_almuerzo')->collapse()->values();

        return $this->showAll($asistencias);
    }
}
