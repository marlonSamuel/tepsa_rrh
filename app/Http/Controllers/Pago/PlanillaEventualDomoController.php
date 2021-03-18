<?php
namespace App\Http\Controllers\Pago;
ini_set('max_execution_time', 1500);

use App\Exports\PlanillaDomoSheetsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Empleado;
use Carbon\Carbon;
use App\PlanillaEventual;
use Illuminate\Http\Request;
use App\PagoEmpleadoDomo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Traits\GlobalFunction;
use Illuminate\Support\Collection;

class PlanillaEventualDomoController extends ApiController
{
   use GlobalFunction;
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

   public function Info($planilla_id,$option){
   		$pago_empleado_domo = PagoEmpleadoDomo::where('planilla_eventual_id',$planilla_id)
   											->with('empleado','planilla_eventual','planilla_eventual.asignacion_domo.cargo')->get();
   		$data = $this->PrepareData($pago_empleado_domo);								
   		return $this->showQuery($data);
   }

   public function PrepareData($pago_empleado_domo){
   	$data = collect();
   	foreach ($pago_empleado_domo as $key => $value) {
   		$cargo = $value->planilla_eventual->asignacion_domo->where('empleado_id',$value->empleado_id)->first();
   		$info = collect([
                    'id'=>$value->id,
                    'codigo'=>$value->empleado_id,
                    'nombre'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                    'dpi'=>' '.$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta,
                    'cargo'=>$cargo->cargo->nombre,
                    'turnos_trabajados'=>$value->conteo_turno,
                    'total_liquido' => $value->total
                ]);
   		$data->push($info);
   		}
   		
   		return $data;
   }

   public function export($id)
    {
    	$planilla = PlanillaEventual::where('id',$id)->firstOrFail();
        $pago_empleado_domo = PagoEmpleadoDomo::where('planilla_eventual_id',$id)
   											->with('empleado','planilla_eventual','planilla_eventual.asignacion_domo.cargo')->get();
   		
        $impresion_planila = $this->PrepareData($pago_empleado_domo);
        $pago_general = $this->pagoCuentasYchequesDomo($planilla->fecha,$pago_empleado_domo);


        $impresion_planila->transform(function($i) {
            unset($i['id']);
            return $i;
        });

        //obtener footer totales
        $impresion_planila = $impresion_planila->merge($this->getSumValues($impresion_planila));
        $pago_general[0] = $pago_general[0]->merge($this->getSumValues($pago_general[0]));
        $pago_general[1] = $pago_general[1]->merge($this->getSumValues($pago_general[1]));
        //$pago_general = $pago_general->merge($this->getSumValues($pago_general));

        $columns_planilla = str_replace( '_', ' ',array_keys($impresion_planila[0]->toArray()));

        $columns_acreditacion_cuentas = count($pago_general[0]) > 0 ? str_replace( '_', ' ',array_keys($pago_general[0][0]->toArray())) : array();

        $columns_acreditacion_cheques = count($pago_general[1]) > 0 ? str_replace( '_', ' ',array_keys($pago_general[1][0]->toArray())) : array();

        $data = [
            'impresion_planila'=>[$columns_planilla,$impresion_planila, $planilla, 'V','IP'],
            'acreditacion_cuentas'=>[$columns_acreditacion_cuentas,$pago_general[0], $planilla,'E','AC'],
            'acreditacion_cheques'=>[$columns_acreditacion_cheques,$pago_general[1], $planilla,'C','ACC']
        ];

        return Excel::download(new PlanillaDomoSheetsExport($data), 'planilla_domo.xlsx');
    }
    public function getSumValues($data)
    {
        $data_return = collect();
        $data_sum = collect();
        if(count($data) > 0){
            foreach (array_keys($data[0]->toArray()) as $d) {
                $data_sum[$d] = '';
                if(is_numeric($data[0][$d]) && $d != 'codigo' && $d != 'dpi' && $d != 'cuenta' && $d != 'cuenta_origen' && $d != 'cuenta_destino')
                {
                    $data_sum[$d] = $data->sum($d);
                }
            } 
        }
        $data_return->push($data_sum);
        return $data_return;
    }
    //imprimir pdf
    public function print($planilla_id,$id=0)
    {

        $planilla = [];
        $detalle = [];

        $planilla = PlanillaEventual::where('id',$planilla_id)->firstOrFail();

        $detalle = PagoEmpleadoDomo::where('planilla_eventual_id',$planilla_id)
   											->with('empleado','planilla_eventual','planilla_eventual.asignacion_domo.cargo')->get();

        if($id>0){
            $detalle = $detalle->where('id',$id);
        }

        $data = $this->PrepareData($detalle);
        
        $pdf = \PDF::loadView('pdfs.print_boleta_domo',['planilla'=>$planilla,'detalle'=>$data]);

        #$pdf->setPaper('legal', 'portrait');

        return $pdf->download('boleta.pdf');
        
    }
}