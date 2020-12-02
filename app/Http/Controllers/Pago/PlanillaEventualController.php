<?php



namespace App\Http\Controllers\Pago;

use App\Turno;
use App\Empleado;
use Carbon\Carbon;
use App\CargoTurno;
use App\Prestacion;
use App\PlanillaEventual;
use App\AsignacionEmpleado;
use App\DetallePagoEventual;
use Illuminate\Http\Request;
use App\PagoEmpleadoEventual;
use App\DetallePagoPrestacion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class PlanillaEventualController extends ApiController
{
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $planillas = PlanillaEventual::all();
        return $this->showAll($planillas);
    }

    //get all payments data
    public function payroll($pagos){
        //data collection to insert data
        $data = collect();

        //get prestacions
        $prestaciones = Prestacion::all();

        foreach ($pagos as $key => $value) {
                //collection of dynamics columns to prestacions
                $prestaciones_col = collect();

                $cargo = '';
                $cargos = $value->detalle_pago->groupBy('cargo_turno.cargo.nombre');

                foreach ($cargos as $key => $c) {
                    $cargo = $cargo.', '.$key;
                }

                $cargo = substr($cargo,2);

                //push general info to collection
                $info = collect([
                    'id'=>$value->id,
                    'codigo'=>$value->empleado_id,
                    'nombre'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                    'afilacion_igss'=>$value->empleado->igss,
                    'dpi'=>$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta,
                    'puesto' => $cargo,
                    'turnos_trabajados'=>$value->total_turnos,
                    'costo_turnos'=>$value->total_monto_turnos,
                    'septimo'=>$value->septimo,
                    'total_devengado' => $value->total_devengado
                ]);

                //merge info and turnos_cols to main data

                //push data to prestaciones_col
                foreach ($prestaciones as $p) {
                     $total_p = $value->prestaciones->where('prestacion_id',$p->id)->sum('total');
                     $p->descripcion = str_replace(' ', '_', $p->descripcion);
                     $prestaciones_col[$p->descripcion] = $total_p;
                }

                //merge main data and prestaciones_col
                $main_data = $info->merge($prestaciones_col);

                //total prestacions
                $main_data['total_prestaciones'] = $value->total_prestaciones;

                //dicounts
                $main_data['descuento_prestaciones'] = $value->descuento_prestaciones;

                $main_data['prestamos'] = $value->prestamos;
                $main_data['alimentos'] = $value->alimentacion;
                $main_data['otros_descuentos'] = $value->otros_descuentos;
                //calculate total page
                $main_data['liquido_a_recibir'] = $value->total_liquidado;

                //push data to data collection
                $data->push($main_data);
                
        }
        return $data;

    }


    //obtener array consolidado, reporte
    public function calculationMaster($pagos){
        //data collection to insert data
        $data = collect();
        //get turns
        $turnos = Turno::all();
        //get prestacions
        $prestaciones = Prestacion::all();
        //get cargo_turnos
        $cargo_turnos = CargoTurno::with('cargo')->get();

        foreach ($pagos as $key => $value) {
            //grouped data by cargo
            $grouped = $value->detalle_pago->groupBy('cargo_turno.cargo.nombre');

            foreach ($grouped as $key2 => $group) {
                //collection of dynamic columns for turns
                $turnos_col = collect();
                $total_turnos = 0;
                $monto_turnos = 0;
                //collection of dynamics columns to prestacions
                $prestaciones_col = collect();

                //push data to turnos_col collection
                foreach ($turnos as $t) {
                    $valor_turno = $cargo_turnos
                                    ->where('cargo.nombre',$key2)
                                    ->where('turno_id',$t->id)->first();
                    $conteo = $group->where('cargo_turno.turno_id',$t->id)->sum('conteo_turnos');
                    $total_turno = $group->where('cargo_turno.turno_id',$t->id)->sum('total');

                    $turnos_col["turno_".$t->numero]=$conteo;
                    $turnos_col["valor_".$t->numero]=$valor_turno->salario;
                    $turnos_col["total_".$t->numero]=$total_turno;
                    $total_turnos+= $conteo;
                    $monto_turnos+= $total_turno;
                }

                $turnos_col["total_turnos"]=$total_turnos;
                $turnos_col["monto_turnos"]=$monto_turnos;

                //recorrer los turnos y pagos por turno
                #$extra_cols =  $turnos_col->merge($prestaciones_col);

                #return $value->prestaciones;

                //push general info to collection
                $info = collect([
                    'id'=>$value->id,
                    'codigo'=>$value->empleado_id,
                    'nombre'=>$value->empleado_id.$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                    'puesto' => $key2,
                    'afilacion_igss'=>$value->empleado->afilacion_igss,
                    'dpi'=>$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta
                ]);

                //merge info and turnos_cols to main data
                $main_data = $info->merge($turnos_col);

                //calculate septimo
                $count_group = count($grouped);

                $main_data['septimo'] = $value->septimo / $count_group;

                $main_data['total_devengado'] = $main_data['monto_turnos'] + $main_data['septimo'];

                $total_prestaciones = 0;
                $descuento_prestaciones = 0;

                //push data to prestaciones_col
                foreach ($prestaciones as $p) {
                     if(!$p->fijo){
                        if($p->descripcion == 'bono 14' || $p->descripcion == 'aguinaldo'){
                            $calculo = ($main_data['total_devengado']*$main_data['total_turnos'])/(365 / $count_group);
                        }else{
                            $calculo = (($p->calculo/30)/24)*8*$main_data['total_turnos'];
                        }
                    }else{
                        $calculo = ($main_data['total_devengado'] * $p->calculo);
                    }


                    $prestaciones_col[$p->descripcion]=$calculo;

                    #calculo de credito o debito de prestaciones
                    if($p->debito_o_credito){
                        $descuento_prestaciones += $calculo;
                    }else{
                        $total_prestaciones+= $calculo;
                    }
                }

                //merge main data and prestaciones_col
                $main_data = $main_data->merge($prestaciones_col);

                //total prestacions
                $main_data['total_prestaciones'] = $total_prestaciones;

                //dicounts
                $main_data['descuento_prestaciones'] = $descuento_prestaciones;

                $main_data['prestamos'] = $value->prestamos / $count_group;
                $main_data['alimentos'] = $value->alimentacion / $count_group;
                $main_data['otros_descuentos'] = $value->otros_descuentos / $count_group;
                //calculate total page
                $main_data['liquido_a_recibir'] = $main_data['total_devengado'] + $main_data['total_prestaciones'] - $main_data['descuento_prestaciones'] - $main_data['prestamos'] - $main_data['alimentos'] - $main_data['otros_descuentos'];

                //push data to data collection
                $data->push($main_data);
            }
        }
        return $data;
    }

        /**
     * Display the specified resource.
     *
     * @param  \App\PlanillaEventual  $planillaEventual
     * @return \Illuminate\Http\Response
     */
    public function show(PlanillaEventual $planillaEventual)
    {
       return $this->showOne($planillaEventual);
    }


    public function info($id,$option){
         $planilla = PlanillaEventual::where('id',$id)
                                      ->with('pago_eventual.empleado',
                                             'pago_eventual.detalle_pago.cargo_turno.cargo',
                                             'pago_eventual.detalle_pago.cargo_turno.turno',
                                             'pago_eventual.prestaciones.prestacion')
                                      ->firstOrFail();

        //option is == 'P' print payroll report else print Master calculation
        if($option == 'P'){
            $data = $this->payroll($planilla->pago_eventual);
        }else{
            $data = $this->calculationMaster($planilla->pago_eventual);
        }
        
        return $this->showQuery($data);
    }

    /**


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'asignacion_empleado_id' => 'required|integer',
            'fecha' => 'required',
            'numero' => 'required',
            'inicio_descarga'=>'required',
            'fin_descarga'=>'required',
            'buque'=>'required'
        ];

        $db = DB::connection('rrh');

        $db->beginTransaction();
            $this->validate($request,$rules);
            $data = $request->all();

            $asignaciones = AsignacionEmpleado::where('id',$request->asignacion_empleado_id)
                                            ->with('detalle_asignacion.carnet','detalle_asignacion','detalle_asignacion.turno','detalle_asignacion.asistencia_turno.cargo_turno')->get()->pluck('detalle_asignacion')->collapse()->values();

            $planilla = PlanillaEventual::create($data);

            #agrupar asignaciones por empleado y por cargo turno
            $asignaciones = $asignaciones->groupBy(['empleado_id','asistencia_turno.cargo_turno_id']);

            foreach ($asignaciones as $key => $value) {
                #crear pago empleado
                $pago = PagoEmpleadoEventual::create([
                            'planilla_eventual_id' => $planilla->id,
                            'empleado_id' => $key,
                            'total_devengado' => 0,
                            'total_turnos' => 0,
                            'total_monto_turnos' => 0,
                            'total_liquidado' => 0,
                            'descuento_prestaciones' => 0,
                            'total_prestaciones' => 0
                        ]);


                #crear detalle pago empleado
                foreach ($value as $key2 => $value2) {
                    $detalle_pago = DetallePagoEventual::create([
                                        'pago_empleado_eventual_id' => $pago->id,
                                        'cargo_turno_id' => $key2,
                                        'conteo_turnos' => count($value2),
                                        'valor_turno' => $value2[0]->asistencia_turno->cargo_turno->salario,
                                        'total' => $value2[0]->asistencia_turno->cargo_turno->salario * count($value2)
                                    ]);
                    $pago->total_turnos += $detalle_pago->conteo_turnos;
                    $pago->total_monto_turnos += $detalle_pago->total;
                }

                $pago->total_devengado = $pago->total_monto_turnos;
                if($detalle_pago->conteo_turnos > 6){
                    $pago->septimo = $pago->total_monto_turnos/6;
                    $pago->total_devengado+=$pago->septimo;
                }
                
                #crear detalle pago o descuento de prestaciones
                $prestaciones = Empleado::where('idEmpleado',$key)
                                            ->with('empleado_prestacion.prestacion')
                                            ->get()
                                            ->pluck('empleado_prestacion')->collapse()->values();

                #calculo prestaciones
                foreach ($prestaciones as $value) {
                    $calculo = 0;
                    if(!$value->prestacion->fijo){
                        if($value->prestacion->descripcion == 'bono 14' || $value->prestacion->descripcion == 'aguinaldo'){
                            $calculo = ($pago->total_devengado*$pago->total_turnos)/365;
                        }else{
                            $calculo = (($value->prestacion->calculo/30)/24)*8*$pago->total_turnos;
                        }
                    }else{
                        $calculo = ($pago->total_devengado * $value->prestacion->calculo);
                    }

                    $pago_prestacion = DetallePagoPrestacion::create([
                        'pago_empleado_eventual_id'=>$pago->id,
                        'prestacion_id'=>$value->prestacion_id,
                        'total'=>$calculo
                    ]);
                   

                    #calculo de credito o debito de prestaciones
                    if($value->prestacion->debito_o_credito){
                        $pago->descuento_prestaciones += $pago_prestacion->total;
                    }else{
                        $pago->total_prestaciones += $pago_prestacion->total;
                    }
                }


                #calculo de totales
                $pago->total_liquidado = $pago->total_devengado + $pago->total_prestaciones - $pago->descuento_prestaciones;
                $pago->save();
            }
        $db->commit();

        return $this->showOne($planilla,201,'insert');   
    }


    /**
     */
    public function update(Request $request, PlanillaEventual $planillaEventual)
    {
        $rules = [
            'fecha' => 'required',
        ];

        $this->validate($request,$rules);

        $planillaEventual->fecha = $request->fecha;

        if(!$planillaEventual->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $planillaEventual->save();

        return $this->showOne($planillaEventual,201,'update');
    }

    /**

     */
    public function destroy(PlanillaEventual $planillaEventual)
    {
        //
    }
}
