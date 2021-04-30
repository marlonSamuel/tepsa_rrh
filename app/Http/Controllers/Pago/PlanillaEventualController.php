<?php



namespace App\Http\Controllers\Pago;
ini_set('max_execution_time', 1500);

use App\Exports\PlanillaEventualSheetsExport;
use Maatwebsite\Excel\Facades\Excel;
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
use App\PagoEmpleadoDomo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Traits\GlobalFunction;

class PlanillaEventualController extends ApiController
{
   use GlobalFunction;

   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $planillas = PlanillaEventual::all();
        return $this->showAll($planillas);
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
                                            ->with('detalle_asignacion.carnet','detalle_asignacion.empleado','detalle_asignacion','detalle_asignacion.turno','detalle_asignacion.asistencia_turno.cargo_turno')->get()->pluck('detalle_asignacion')->collapse()->values();

            $asignaciones = $asignaciones->where('empleado.tipo_empleado',0)->values();

            $asignaciones = $asignaciones->where('asistencia_turno','!=',null)->values();

            $asignaciones = $asignaciones->filter(function($item) {
                return $item->asistencia_turno->bloqueado == 0 || ($item->asistencia_turno->bloqueado & $item->asistencia_turno->desbloqueado);
            });

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
                            'total_prestaciones' => 0,
                            'bono_turnos' => 0
                        ]);

                #crear detalle pago empleado
                foreach ($value as $key2 => $value2) {
                    $detalle_pago = DetallePagoEventual::create([
                                        'pago_empleado_eventual_id' => $pago->id,
                                        'cargo_turno_id' => $key2,
                                        'conteo_turnos' => count($value2),
                                        'valor_turno' => $value2[0]->asistencia_turno->cargo_turno->salario,
                                        'total' => $value2[0]->asistencia_turno->cargo_turno->salario * count($value2),
                                        'bono_turno' => count($value2) * $request->bono_turno
                                    ]);

                    $pago->total_turnos += $detalle_pago->conteo_turnos;
                    $pago->total_monto_turnos += $detalle_pago->total;
                    $pago->bono_turnos += $detalle_pago->bono_turno;
                }

                $pago->total_devengado = $pago->total_monto_turnos + $pago->bono_turnos;

                if($pago->total_turnos >= 6){
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
                        if(strtolower($value->prestacion->descripcion) != "isr"){
                            $calculo = ($pago->total_devengado * $value->prestacion->calculo);
                        }
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

        $this->pago_domo($request->asignacion_empleado_id,$planilla->id);
        $db->commit();

        return $this->showOne($planilla,201,'insert');   
    }

    public function pago_domo($asignacion_empleado_id,$planilla_id){
        
        $db = DB::connection('rrh');
        $db->beginTransaction();
            
        $asignaciones = AsignacionEmpleado::where('id',$asignacion_empleado_id)
                                            ->with('asignacion_domos.carnet','asignacion_domos','asignacion_domos.asistencia_domo','asignacion_domos.cargo')->get()->pluck('asignacion_domos')->collapse()->values();
                                            #agrupar asignaciones por empleado y por cargo turno
        $asignaciones = $asignaciones->groupBy(['empleado_id','asignacion_domos.asistencia_domo.turno']);
        
        foreach ($asignaciones as $key => $value) {
            
            $total = 0;
            $conteo = 0;
            foreach ($value as $key2 => $value2) {               
                foreach ($value2 as $key3 => $value3) {
                    $conteo = $conteo + count($value3->asistencia_domo);
                    $total = $total + ($value3->cargo->salario * count($value3->asistencia_domo));
                }               
            }
            if ($conteo > 0) {
                $pago = PagoEmpleadoDomo::create([
                            'planilla_eventual_id' => $planilla_id,
                            'empleado_id' => $key,
                            'conteo_turno' => 0,
                            'total' => 0,
                        ]);
                $pago->total = $total;
                $pago->conteo_turno = $conteo;
                $pago->confirmar_pago = true;

                //dd($conteo);
                $pago->save();
            }
            
        }
        $db->commit();

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
        $planillaEventual->delete();
        return $this->showOne($planillaEventual,201,'delete');
    }

    public function export($id)
    {
        $planilla = PlanillaEventual::where('id',$id)
                              ->with('pago_eventual.empleado',
                                     'pago_eventual.detalle_pago.cargo_turno.cargo',
                                     'pago_eventual.detalle_pago.cargo_turno.turno',
                                     'pago_eventual.prestaciones.prestacion')
                                      ->firstOrFail();

        $impresion_planila = $this->payroll($planilla->pago_eventual);
        $maestro_calculos = $this->calculationMaster($planilla->pago_eventual);
        $pago_general = $this->pagoCuentasYcheques($planilla->fecha,$planilla->pago_eventual);


        $impresion_planila->transform(function($i) {
            unset($i['id']);
            return $i;
        });

        $maestro_calculos->transform(function($i) {
            unset($i['id']);
            return $i;
        });


        //$planilla_2_turnos = $maestro_calculos->where('turno_4','>',0)->orWhere('turno_5','>',0);
        //$planilla_3_turnos = $maestro_calculos->where('turno_1','>',0)->orWhere('turno_2','>',0)->orWhere('turno_3','>',0);

        //return $this->showQuery($planilla_2_horas);


        $impresion_planila = $impresion_planila->merge($this->getSumValues($impresion_planila));
        $maestro_calculos = $maestro_calculos->merge($this->getSumValues($maestro_calculos));
        $pago_general[0] = $pago_general[0]->merge($this->getSumValues($pago_general[0]));
        $pago_general[1] = $pago_general[1]->merge($this->getSumValues($pago_general[1]));
        

        $columns_planilla = str_replace( '_', ' ',array_keys($impresion_planila[0]->toArray()));
        $columns_calculos = str_replace( '_', ' ',array_keys($maestro_calculos[0]->toArray()));

        $columns_acreditacion_cuentas = count($pago_general[0]) > 0 ? str_replace( '_', ' ',array_keys($pago_general[0][0]->toArray())) : array();

        $columns_acreditacion_cheques = count($pago_general[1]) > 0 ? str_replace( '_', ' ',array_keys($pago_general[1][0]->toArray())) : array();

        $data = [
            'impresion_planilla'=>[$columns_calculos,$maestro_calculos, $planilla,'AK','MC'],
            'planilla_resumen'=>[$columns_planilla,$impresion_planila, $planilla, 'V','IP'],
            'acreditacion_cuentas'=>[$columns_acreditacion_cuentas,$pago_general[0], $planilla,'E','AC'],
            'acreditacion_cheques'=>[$columns_acreditacion_cheques,$pago_general[1], $planilla,'C','ACC']
        ];

        return Excel::download(new PlanillaEventualSheetsExport($data), 'planilla_eventual.xlsx');
    }

    public function getSumValues($data)
    {
        $data_return = collect();
        $data_sum = collect();
        $data_sum2 = collect();
        $data_sum3 = collect();

        $count = 0;
        if(count($data) > 0){
            foreach (array_keys($data[0]->toArray()) as $d) {
                $data_sum[$d] = '';
                $data_sum2[$d] = '';
                $data_sum3[$d] = '';


                if($count == 5){
                    $data_sum[$d] = "Totales generales";
                    $data_sum2[$d] = "Pagos a cuenta BAC";
                    $data_sum3[$d] = "Pagos con cheque";
                }

                if(is_numeric($data[0][$d]) && $d != 'codigo' && $d != 'cuenta_origen' && $d != 'cuenta_destino' && $d != 'cuenta' && $d != 'dpi')
                {
                    $data_sum[$d] = $data->sum($d);

                    if(isset($data[0]["cuenta"])){

                        $data_sum2[$d] = $data->where('cuenta','!=','')->sum($d);
                        $data_sum3[$d] = $data->where('cuenta','==','')->sum($d); 
                    }
                }
                $count++;
            } 
        }


        $data_return->push($data_sum);
        $data_return->push($data_sum2);
        $data_return->push($data_sum3);

        return $data_return;
    }
}
