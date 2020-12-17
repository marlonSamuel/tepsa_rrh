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


        $columns_planilla = array_keys($impresion_planila[0]->toArray());
        $columns_calculos = array_keys($maestro_calculos[0]->toArray());
        $columns_acreditacion_cuentas = array_keys($pago_general[0][0]->toArray());
        $columns_acreditacion_cheques = array_keys($pago_general[1][0]->toArray());

        //return $this->showQuery($pago_general);

        $data = [
            'impresion_planila'=>[$columns_planilla,$impresion_planila, $planilla],
            'maestro_calculos'=>[$columns_calculos,$maestro_calculos, $planilla],
            'acreditacion_cuentas'=>[$columns_acreditacion_cuentas,$pago_general[0], $planilla],
            'acreditacion_cheques'=>[$columns_acreditacion_cheques,$pago_general[1], $planilla]
        ];

        return Excel::download(new PlanillaEventualSheetsExport($data), 'planilla_eventual.xlsx');

        

    }
}
