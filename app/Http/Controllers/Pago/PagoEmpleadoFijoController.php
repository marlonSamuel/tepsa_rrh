<?php

namespace App\Http\Controllers\Pago;

use App\Exports\PlanillaFijoSheetsExport;
use Maatwebsite\Excel\Facades\Excel;
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
use App\Traits\GlobalFunction;

class PagoEmpleadoFijoController extends ApiController
{
    use GlobalFunction;
    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }


    public function index()
    {
        $now = Carbon::now();
        $year = Anio::where('anio', $now->year)->first();

        $quincenas = Quincena::where('anio_id', 2)->with('pago_empleado_fijo', 'anio', 'mes')->get();
        return $this->showAll($quincenas);
    }

    public function info($quincena_id, $option)
    {
        $pagoEmpleadoFijo = PagoEmpleadoFijo::where('quincena_id', $quincena_id)
            ->with('detalle_pago', 'detalle_pago.prestacion', 'quincena', 'empleado', 'empleado.Cargo')->get();
        if ($option == 'P') {
            $data = $this->payrollFijo($pagoEmpleadoFijo);
        }

        return $this->showQuery($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PagoEmpleadoFijo  $pagoEmpleadoFijo
     * @return \Illuminate\Http\Response
     */
    public function show($quincena_id)
    {
        $pagoEmpleadoFijo = PagoEmpleadoFijo::where('quincena_id', $quincena_id)
            ->with('detalle_pago', 'detalle_pago.prestacion', 'quincena', 'empleado', 'empleado.Cargo')->get();

        $data = $this->payrollFijo($pagoEmpleadoFijo);
        return $this->showQuery($data);
    }
    public function store(Request $request)
    {
        $rules = [
            'quincena_id' => 'required|integer'
        ];
        $db = DB::connection('rrh');

        $db->beginTransaction();
        $this->validate($request, $rules);

        $empleados = Empleado::where('tipo_empleado', true)
            ->with('Cargo', 'empleado_prestacion.prestacion')
            ->get();
        $quincena = Quincena::where('id', $request->quincena_id)
            ->with('mes', 'anio')->first();
        $pagoPrimerQuincena = [];
        if ($quincena->fin_mes) {
            $quincena_anterior = Quincena::where('id', $request->quincena_id - 1)->firstOrFail();
            $data = PagoEmpleadoFijo::where('quincena_id', $quincena_anterior->id)->get();
            $pagoPrimerQuincena = $data;
        }

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
                        if ($quincena->mes->id == 7 && $value2->prestacion->descripcion == 'bono 14') {
                            $detalle_pago = DetallePagoEmpleadoFijo::create([
                                'pago_empleado_fijo_id' => $pago->id,
                                'prestacion_id' => $value2->prestacion_id,
                                'total' => $value->Cargo->salario
                            ]);
                            $prestacion_debito = $prestacion_debito+$value->Cargo->salario;
                            $detalle_pago->save();
                        } elseif ($quincena->mes->id == 12 && $value2->prestacion->descripcion == 'aguinaldo') {
                            $detalle_pago = DetallePagoEmpleadoFijo::create([
                                'pago_empleado_fijo_id' => $pago->id,
                                'prestacion_id' => $value2->prestacion_id,
                                'total' => $value->Cargo->salario
                            ]);
                            $prestacion_debito = $prestacion_debito+$value->Cargo->salario;
                            $detalle_pago->save();
                        }
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
            } /*else {
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
                } elseif ($quincena->mes->id == 12) {
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
            }*/

            if ($quincena->fin_mes) {
                foreach ($pagoPrimerQuincena as $index => $itemPrimerQ) {

                    if ($value->idEmpleado == $itemPrimerQ->empleado_id) {

                        $total = $value->Cargo->salario - $itemPrimerQ->total + $prestacion_debito - $prestacion_credito;
                    }
                }
            } else {
                $total = ($value->Cargo->salario / 2) + $prestacion_debito - $prestacion_credito;
            }

            $pago->total = $total;
            $pago->save();
        }
        $quincena->cerrada = true;
        $quincena->save();
        $db->commit();
    }
    public function update(Request $request, PagoEmpleadoFijo $pago_empleado_fijo)
    {

        //actualizamos a pago anterior       
        $pago_empleado_fijo->otro_ingreso = $request->otro_ingreso;
        $pago_empleado_fijo->anticipo = $request->anticipo;
        $pago_empleado_fijo->hora_extra_simple = $request->hora_extra_simple;
        $pago_empleado_fijo->hora_extra_doble = $request->hora_extra_doble;
        $pago_empleado_fijo->otro_descuento = $request->otro_descuento;

        //restamos los descuentos necesarios
        $pago_empleado_fijo->total = $pago_empleado_fijo->total + $request->otro_ingreso + $request->anticipo + $request->hora_extra_simple + $request->hora_extra_doble - $request->otro_descuento;


        if (!$pago_empleado_fijo->isDirty()) {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar', 422);
        }

        $pago_empleado_fijo->save();

        return $this->showOne($pago_empleado_fijo, 201, 'update');
    }

    public function getQuincenas($mes_id)
    {

        $now = Carbon::now();
        $year = Anio::where('anio', $now->year)->first();
        $quincenas = Quincena::where([['anio_id', $year->id], ['mes_id', $mes_id]])->get();
        return $this->showAll($quincenas);
    }
    public function getPlanilla($quincena_id)
    {

        $quincena = Quincena::where('id', $quincena_id)->with('anio','mes')->first();
        return $this->showOne($quincena);
    }
    public function getMes()
    {
        $now = Carbon::now();
        $meses = Mes::where('id', '<=', $now->month)->get();
        return $this->showAll($meses);
    }
    public function export($quincena_id)
    {
        $planilla = Quincena::where('id', $quincena_id)->with('anio', 'mes')->firstOrFail();
        $pagoPrimerQuincena = [];
        if ($planilla->fin_mes) {
            $quincena_anterior = Quincena::where('id', $planilla->id - 1)->firstOrFail();
            $data = PagoEmpleadoFijo::where('quincena_id', $quincena_anterior->id)->get();
            $pagoPrimerQuincena = $data;
        }
        $pagoEmpleadoFijo = PagoEmpleadoFijo::where('quincena_id', $quincena_id)
            ->with('detalle_pago', 'detalle_pago.prestacion', 'quincena', 'empleado', 'empleado.Cargo')->get();
            
            foreach ($pagoEmpleadoFijo as $key => $value) {
                foreach ($pagoPrimerQuincena as $key2 => $value2) {
                    if ($value->empleado_id == $value2->empleado_id) {
                        $pagoEmpleadoFijo[$key]->anticipo = $value2->total;
                    }
                }
            }       
        $impresion_planila = $this->payrollFijo($pagoEmpleadoFijo);
        /*$maestro_calculos = $this->calculationMaster($planilla->pago_eventual);
        */
        $pago_general = $this->pagoCuentasYchequesFijos($planilla->fecha_fin,$pagoEmpleadoFijo);

        $columns_planilla = array_keys($impresion_planila[0]->toArray());
        /*       $columns_calculos = array_keys($maestro_calculos[0]->toArray());
        */
        $columns_acreditacion_cuentas = array_keys($pago_general[0][0]->toArray());
        //$columns_acreditacion_cheques = array_keys($pago_general[1][0]->toArray());

        //return $this->showQuery($pago_general);

        $data = [
            'impresion_planila' => [$columns_planilla, $impresion_planila, $planilla],
            /* 'maestro_calculos'=>[$columns_calculos,$maestro_calculos, $planilla],*/
            'acreditacion_cuentas'=>[$columns_acreditacion_cuentas,$pago_general[0], $planilla],
            //'acreditacion_cheques'=>[$columns_acreditacion_cheques,$pago_general[1], $planilla]
        ];

        return Excel::download(new PlanillaFijoSheetsExport($data), 'planilla_fijos.xlsx');
    }
    public function print($quincena_id, $id = 0)
    {

        $planilla = [];
        $detalle = [];
        $planilla = Quincena::where('id', $quincena_id)->with('anio', 'mes')->firstOrFail();
        $quincena_anterior = Quincena::where('id', $quincena_id - 1)->firstOrFail();

        $planilla->fecha_inicio = Carbon::createFromFormat('m/Y', $planilla->mes->id . '/' . $planilla->anio->anio)->firstOfMonth();
        $planilla->fecha_fin = Carbon::createFromFormat('m/Y', $planilla->mes->id . '/' . $planilla->anio->anio)->lastOfMonth();

        $pagoEmpleadoFijo = PagoEmpleadoFijo::where('quincena_id', $quincena_id)
            ->with('detalle_pago', 'detalle_pago.prestacion', 'quincena', 'empleado', 'empleado.Cargo')->get();
        $pagoPrimerQuincena = PagoEmpleadoFijo::where('quincena_id', $quincena_anterior->id)->get();

        foreach ($pagoEmpleadoFijo as $key => $value) {
            foreach ($pagoPrimerQuincena as $key2 => $value2) {
                if ($value->empleado_id == $value2->empleado_id) {
                    $pagoEmpleadoFijo[$key]->anticipo = $value2->total;
                }
            }
        }

        if ($id > 0) {
            $pagoEmpleadoFijo = $pagoEmpleadoFijo->where('id', $id);
        }

        $data = $this->payrollFijo($pagoEmpleadoFijo);


        $pdf = \PDF::loadView('pdfs.print_boleta_fijo', ['planilla' => $planilla, 'detalle' => $data]);

        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('boleta.pdf');
    }
}
