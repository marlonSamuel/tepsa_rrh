<?php

namespace App\Http\Controllers\Pago;
ini_set('max_execution_time', 1500);

use App\PagoEmpleadoEventual;
use App\PlanillaEventual;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Traits\GlobalFunction;

class PagoEmpleadoEventualController extends ApiController
{
    use GlobalFunction;

    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $pagoEmpleadoEventuals = PagoEmpleadoEventual::all();
        return $this->showAll($pagoEmpleadoEventuals);
    }


    /**
     */
    public function store(Request $request)
    {

    }

    /**
     */
    public function show(PagoEmpleadoEventual $pago_empleado_eventual)
    {
        return $this->showOne($pagoEmpleadoEventual,200,'select');
    }

    /**
     */
    public function update(Request $request, PagoEmpleadoEventual $pago_empleado_eventual)
    {
        
        //actualizamos a pago anterior
        $pago_empleado_eventual->total_liquidado = $pago_empleado_eventual->total_liquidado + $pago_empleado_eventual->prestamos + $pago_empleado_eventual->alimentacion + $pago_empleado_eventual->otros_descuentos;

        $pago_empleado_eventual->prestamos = $request->prestamos;
        $pago_empleado_eventual->alimentacion = $request->alimentacion;
        $pago_empleado_eventual->otros_descuentos = $request->otros_descuentos;


        //restamos los descuentos necesarios
        $pago_empleado_eventual->total_liquidado = $pago_empleado_eventual->total_liquidado - $request->prestamos - $request->alimentacion - $request->otros_descuentos;

        
         if(!$pago_empleado_eventual->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $pago_empleado_eventual->save();

        return $this->showOne($pago_empleado_eventual,201,'update');

    }

    /**
     */
    public function destroy(pagoEmpleadoEventual $pago_empleado_eventual)
    {
        $pago_empleado_eventual->delete();

        return $this->showOne($pago_empleado_eventual,201,'delete');
    }

    //imprimir pdf
    public function print($planilla_id,$id=0)
    {

        $planilla = [];
        $detalle = [];

        $planilla = PlanillaEventual::where('id',$planilla_id)->firstOrFail();

        $detalle = $planilla->pago_eventual()->with('empleado',
                                             'detalle_pago.cargo_turno.cargo',
                                             'detalle_pago.cargo_turno.turno',
                                             'prestaciones.prestacion')->get();

        if($id>0){
            $detalle = $detalle->where('id',$id);
        }

        $data = $this->calculationMaster($detalle);

        $pdf = \PDF::loadView('pdfs.print_boleta',['planilla'=>$planilla,'detalle'=>$data]);

        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('boleta.pdf');
        
    }
}
