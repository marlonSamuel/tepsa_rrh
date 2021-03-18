<?php

namespace App\Http\Controllers\Asignacion;

use App\Carnet;
use App\Turno;
use App\PlanoEstiba;
use Barryvdh\DomPDF\PDF;
use App\AsignacionEmpleado;
use Illuminate\Http\Request;
use App\DetalleAsignacionEmpleado;
use App\AsignacionDomo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class AsignacionEmpleadoController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $asignaciones = AsignacionEmpleado::with('planificacion.buque','detalle_asignacion.asistencia_turno.cargo_turno.cargo','detalle_asignacion.turno','detalle_asignacion.empleado','detalle_asignacion.asistencia_almuerzo','asignacion_domos.asistencia_domo','asignacion_domos.empleado')->get();

        $asignaciones = $asignaciones->sortByDesc('id')->values();
        return $this->showAll($asignaciones);
    }

    public function showOneRow($id)
    {
        $asignacion_empleado = AsignacionEmpleado::where('id',$id)
                                                    ->with('planificacion.buque','detalle_asignacion.asistencia_turno.cargo_turno.cargo','detalle_asignacion.turno','detalle_asignacion.empleado','detalle_asignacion.asistencia_almuerzo','asignacion_domos.asistencia_domo','asignacion_domos.empleado')->first();

        return $this->showOne($asignacion_empleado);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'planificacion_id' => 'required|integer',
            'turno_id'=>'required',
            'carnet_id'=>'required',
            'empleado_id'=>'required',
            'fecha'=>'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();

            $this->validate($request,$rules);

            $data = $request->all();

            $exits_domo = AsignacionDomo::where([['empleado_id',$request->empleado_id],['fecha',$request->fecha]])
                                                     ->first();

            if($exits_domo) return $this->errorResponse('empleado ya tiene una asignacion a domo en esta fecha ',421);

            $exits = DetalleAsignacionEmpleado::where([['empleado_id',$request->empleado_id],/*['turno_id',$request->turno_id],*/['fecha',$request->fecha]])
                                                     ->get();

            $turno_add = Turno::find($request->turno_id);  

            foreach ($exits as $t) {
                $turno = Turno::find($t->turno_id);

                if($turno->numero == $turno_add->numero) return $this->errorResponse('empleado ya ha sido asignado a este turno en la fecha que trata de asignar','421');

                if(($turno->numero == 1) && ($turno_add->numero == 4)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 2) && ($turno_add->numero == 4 || $turno_add->numero == 5)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 3) && ($turno_add->numero == 5)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 4) && ($turno_add->numero == 1 || $turno_add->numero == 2)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 5) && ($turno_add->numero == 2 || $turno_add->numero == 3)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');
                
            }

            $asignacion = AsignacionEmpleado::create($data);

            $data_d = new DetalleAsignacionEmpleado;
            $data_d->turno_id = $request->turno_id;
            $data_d->asignacion_empleado_id = $asignacion->id;
            $data_d->carnet_id = $request->carnet_id;
            $data_d->empleado_id = $request->empleado_id;
            $data_d->fecha = $request->fecha;

            $data_d->save();

            $carnet = Carnet::find($request->carnet_id);
            $carnet->asignado = true;
            $carnet->save();
        $db->commit();

        return $this->showOne($asignacion,201,'insert');
    }


    //obtener un solo registro para planilla
    public function asignacion($date,$buque_id){
        $planificacion = PlanoEstiba::where([['fecha_atraque',$date],['idBuque',$buque_id]])->with('buque','asignacion.detalle_asignacion','asignacion.planilla_eventual')->first();

        if(is_null($planificacion)) return $this->errorResponse('no se encontró ninguna importaci con los datos especificados',404);

        if(is_null($planificacion->asignacion))return $this->errorResponse('no se encontró ninguna asignación a la importación con los datos especificados',404);

        $planilla_exists = $planificacion->asignacion->planilla_eventual;

        if(!is_null($planilla_exists)){
            return $this->errorResponse('Planilla ya fué registrada',422);
        }
        
        return $this->showOne($planificacion);
    }

    /**
     */
    public function show(AsignacionEmpleado $asignacion_empleado)
    {
        $asignacion_empleado = AsignacionEmpleado::where('id',$asignacion_empleado->id)
                                                    ->with('detalle_asignacion.turno')->get()->pluck('detalle_asignacion','asignacion_domos')->collapse();

        return $this->showAll($asignacion_empleado,200,'select');
    }

    public function showAsignacionDomo($id)
    {
        $asignacion_empleado = AsignacionEmpleado::where('id',$id)
                                                    ->with('asignacion_domos.empleado',
                                                           'asignacion_domos.cargo',
                                                           'asignacion_domos.carnet',
                                                           'planificacion.buque')->firstOrFail();

        return $this->showOne($asignacion_empleado,200,'select');
    }

    //obtener asignacion por turno e id en detalle asignacion empleado
    public function getDataTurn($id,$turno_id,$fecha){
        $detalle = DetalleAsignacionEmpleado::where([['asignacion_empleado_id',$id],['turno_id',$turno_id],['fecha',$fecha]])->with('empleado','turno','carnet','asignacion.planificacion')->get();

        return $this->showall($detalle);
    }

    //obtener asignaciones por asignacion y empleado
    public function getByEmpleadoAsignacion($id,$empleado_id){
        $asignacion = DetalleAsignacionEmpleado::where([['empleado_id',$empleado_id],['asignacion_empleado_id',$id]])
                                                     ->with('carnet')->first();

        return $this->showQuery($asignacion);
    }

    /**
     */
    public function update(Request $request, AsignacionEmpleado $asignacion_empleado)
    {
        $rules = [
            'planificacion_id' => 'required|integer',
            'turno_id'=>'required',
            'carnet_id'=>'required',
            'empleado_id'=>'required',
            'fecha'=>'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();
            $this->validate($request,$rules);

            $exits_domo = AsignacionDomo::where([['empleado_id',$request->empleado_id],['fecha',$request->fecha]])
                                                     ->first();

            if($exits_domo) return $this->errorResponse('empleado ya tiene una asignacion a domo en esta fecha ',421);

            $exits = DetalleAsignacionEmpleado::where([['empleado_id',$request->empleado_id],/*['turno_id',$request->turno_id],*/['fecha',$request->fecha]])
                                                     ->get();

            $turno_add = Turno::find($request->turno_id);  

            foreach ($exits as $t) {
                $turno = Turno::find($t->turno_id);

                if($turno->numero == $turno_add->numero) return $this->errorResponse('empleado ya ha sido asignado a este turno en la fecha que trata de asignar','421');

                if(($turno->numero == 1) && ($turno_add->numero == 4)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 2) && ($turno_add->numero == 4 || $turno_add->numero == 5)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 3) && ($turno_add->numero == 5)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 4) && ($turno_add->numero == 1 || $turno_add->numero == 2)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');

                if(($turno->numero == 5) && ($turno_add->numero == 2 || $turno_add->numero == 3)) return $this->errorResponse('empleado ya ha sido asignado a el turno '.$turno->numero.' en la fecha que trata de asignar','421');
                
            }

            $data_d = new DetalleAsignacionEmpleado;
            $data_d->turno_id = $request->turno_id;
            $data_d->asignacion_empleado_id = $asignacion_empleado->id;
            $data_d->carnet_id = $request->carnet_id;
            $data_d->empleado_id = $request->empleado_id;
            $data_d->fecha = $request->fecha;

            $data_d->save();

            $carnet = Carnet::find($request->carnet_id);
            $carnet->asignado = true;
            $carnet->save();

        $db->commit();

        return $this->showOne($asignacion_empleado,201,'update');

    }

    /**
     */
    public function destroy(AsignacionEmpleado $asignacion_empleado)
    {
        DB::beginTransaction();

        //
        $carnets_muelle = $asignacion_empleado->detalle_asignacion()->with('carnet')->get();

        foreach ($carnets_muelle as $c) {
            $c->carnet->asignado = false;
            $c->carnet->save();
        }

        $carnets_domo = $asignacion_empleado->asignacion_domos()->with('carnet')->get();
        foreach ($carnets_domo as $carnet) {
            $c->carnet->asignado = false;
            $c->carnet->save();
        }

        $asignacion_empleado->delete();
        DB::commit();
        return $this->showOne($asignacion_empleado,201,'delete');
    }

    //release cards from asignations
    public function releaseCards($id){
        //
        $asignacion_empleado = AsignacionEmpleado::find($id);
        DB::beginTransaction();
        $carnets_muelle = $asignacion_empleado->detalle_asignacion()->with('carnet')->get();

        foreach ($carnets_muelle as $c) {
            $c->carnet->asignado = false;
            $c->carnet->save();
        }

        $carnets_domo = $asignacion_empleado->asignacion_domos()->with('carnet')->get();
        foreach ($carnets_domo as $carnet) {
            $c->carnet->asignado = false;
            $c->carnet->save();
        }

        $asignacion_empleado->terminada = true;
        $asignacion_empleado->save();

        DB::commit();
        return $this->showOne($asignacion_empleado,201,'update');
    }

    //imprimir pdf
    public function print($id,$turno_id,$fecha,$empleado_id=0)
    {
        $asignacion = AsignacionEmpleado::where('id',$id)
                                         ->with('planificacion.buque')
                                         ->firstOrFail();

        $detalle = DetalleAsignacionEmpleado::where([['asignacion_empleado_id',$id],['turno_id',$turno_id],['fecha',$fecha]])->with('empleado','turno','carnet')->get();


        if($empleado_id > 0){
            $detalle = $detalle->where('empleado_id',$empleado_id);
        }

        $pdf = \PDF::loadView('pdfs.print_asignacion',['asignacion'=>$asignacion,'detalle'=>$detalle])->setPaper('a4', 'landscape');


        
        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('ejemplo.pdf');
        
    }
}
