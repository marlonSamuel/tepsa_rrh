<?php

namespace App\Http\Controllers\Pago;

use App\Cargo;
use App\Empleado;
use App\Mes;
use App\Anio;
use App\EmpleadoPrestacion;
use App\DetallePagoEmpleadoFijo;
use App\Http\Controllers\ApiController;
use App\PagoEmpleadoFijo;
use App\Quincena;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoEmpleadoFijoController extends ApiController
{
    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }


    public function index(){
    	$now = Carbon::now();
    	$year = Anio::where('anio',$now->year)->first();

    	$quincenas = Quincena::where('anio_id',$year->id)->with('pago_empleado_fijo','anio','mes')->get();
    	return $this->showAll($quincenas);
    }

            /**
     * Display the specified resource.
     *
     * @param  \App\PagoEmpleadoFijo  $pagoEmpleadoFijo
     * @return \Illuminate\Http\Response
     */
    public function show($quincena_id)
    {
    	$pagoEmpleadoFijo = PagoEmpleadoFijo::where('quincena_id',$quincena_id)
    						->with('detalle_pago','detalle_pago.prestacion','quincena','empleado')->get();
       return $this->showAll($pagoEmpleadoFijo);
    }
    public function store(Request $request)
    {
    	$rules = [
            'quincena_id' => 'required|integer'
        ];
        $db = DB::connection('rrh');

        $db->beginTransaction();
        $this->validate($request,$rules);

        $empleados = Empleado::where('tipo_empleado', true)
            ->with('Cargo', 'empleado_prestacion.prestacion')
            ->get();
        $quincena = Quincena::where('id', $request->quincena_id)
            ->with('mes', 'anio')->first();

        foreach ($empleados as $key => $value) {
            $pago = PagoEmpleadoFijo::create([
                'empleado_id' => $value->idEmpleado,
                'quincena_id' => $quincena->id,
                'cargo_id' => $value->idCargo,
                'total' => 0,
                'otro_descuento' => 0,
                'anticipo' => 0,
                'otro_ingreso' => 0,
                'hora_extra_simple' => 0,
                'hora_extra_doble' => 0,
            ]);

            $prestacion_debito = 0;
            $prestacion_credito = 0;
            if ($quincena->fin_mes == true) {
                foreach ($value->empleado_prestacion as $key2 => $value2) {
                    if ($value2->prestacion->descripcion == 'bono 14' || $value2->prestacion->descripcion == 'aguinaldo') {
                        # code...
                    } else {
                        $detalle_pago = DetallePagoEmpleadoFijo::create([
                            'pago_empleado_fijo_id' => $pago->id,
                            'prestacion_id' => $value2->prestacion_id,
                            'total' => $value2->prestacion->fijo ? ($value->Cargo->salario * $value2->prestacion->calculo) : $value2->prestacion->calculo
                        ]);
                        if (!$value2->prestacion->debito_o_credito) {
                            //$prestacion_debito = $prestacion_debito + $value2->prestacion->calculo;
                            if ($value2->prestacion->fijo) {
                                $prestacion_debito = $prestacion_debito + ($value->Cargo->salario * $value2->prestacion->calculo);
                            } else {
                                $prestacion_debito = $prestacion_debito + $value2->prestacion->calculo;
                            }
                        } else {
                            if ($value2->prestacion->fijo) {
                                $prestacion_credito = $prestacion_credito + ($value->Cargo->salario * $value2->prestacion->calculo);
                            } else {
                                $prestacion_credito = $prestacion_credito + $value2->prestacion->calculo;
                            }
                        }
                        $detalle_pago->save();
                    }
                }
            } else {
                if ($quincena->mes->id == 7) {
                    foreach ($value->empleado_prestacion as $key2 => $value2) {
                        if ($value2->prestacion->descripcion == 'bono 14') {
                            $detalle_pago = DetallePagoEmpleadoFijo::create([
                                'pago_empleado_fijo_id' => $pago->id,
                                'prestacion_id' => $value2->prestacion_id,
                                'total' => $value->Cargo->salario
                            ]);
                            $prestacion_debito = $value->Cargo->salario;
                            $detalle_pago->save();
                        }
                    }
                }elseif ($quincena->mes->id == 12) {
                    foreach ($value->empleado_prestacion as $key2 => $value2) {
                        if ($value2->prestacion->descripcion == 'aguinaldo') {
                            $detalle_pago = DetallePagoEmpleadoFijo::create([
                                'pago_empleado_fijo_id' => $pago->id,
                                'prestacion_id' => $value2->prestacion_id,
                                'total' => $value->Cargo->salario
                            ]);
                            $prestacion_debito = $value->Cargo->salario;
                            $detalle_pago->save();
                        }
                    }
                }
            }
            $total = ($value->Cargo->salario / 2) + $prestacion_debito - $prestacion_credito;
            $pago->total = $total;
            $pago->save();
        }
        $db->commit();
    }
    public function update(Request $request, PagoEmpleadoFijo $pago_empleado_fijo)
    {
        
        //actualizamos a pago anterior       
        $pago_empleado_fijo->otro_ingreso = $request->otro_ingreso; 
        $pago_empleado_fijo->anticipo = $request->anticipo;
        $pago_empleado_fijo->hora_extra_simple = $request->hora_extra_simple;
        $pago_empleado_fijo->hora_extra_doble= $request->hora_extra_doble;
        $pago_empleado_fijo->otro_descuento = $request->otro_descuento;

        //restamos los descuentos necesarios
        $pago_empleado_fijo->total = $request->otro_ingreso+$request->anticipo+$request->hora_extra_simple + $request->hora_extra_doble - $request->otro_descuento;

        
         if(!$pago_empleado_fijo->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $pago_empleado_fijo->save();

        return $this->showOne($pago_empleado_fijo,201,'update');

    }

    public function getQuincenas($mes_id){

    	$now = Carbon::now();
    	$year = Anio::where('anio',$now->year)->first();
    	$quincenas = Quincena::where([['anio_id',$year->id],['mes_id',$mes_id]])->get();
    	return $this->showAll($quincenas);
    }
    public function getMes(){
    	$now = Carbon::now();
    	$meses = Mes::where('id','<=', $now->month)->get();
    	return $this->showAll($meses);
    }
}
