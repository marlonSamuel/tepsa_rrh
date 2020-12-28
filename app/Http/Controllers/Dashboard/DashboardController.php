<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Empleado;
use App\PlanillaEventual;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends ApiController
{
    public function __construct()
    {
     #   parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $empleados = Empleado::all();
        $total = $empleados->count();
        $fijos = $empleados->where('tipo_empleado',true)->count();
        $eventuales = $empleados->where('tipo_empleado',false)->count();


        $year = Carbon::now()->year;

        $planilla_eventuales = PlanillaEventual::whereYear('fecha', '=', $year)
				              ->with('pago_eventual')->get();
		$pago_planillas = $planilla_eventuales->pluck('pago_eventual')->collapse()->sum('total_liquidado');

        return response()->json(
        	['total' => $total,
        	 'fijos' => $fijos,
        	 'eventuales' => $eventuales,
        	 'planilla_eventuales' => $planilla_eventuales->count(),
        	 'total_planilla' => $pago_planillas]
        );
    }

    public function groupByPlanilla()
    {
    	$year = Carbon::now()->year;

    	$planillas = DB::table('planilla_eventuals as p')
        					   ->join('pago_empleado_eventuals as pp','p.id','=','pp.planilla_eventual_id')
							   ->select('p.numero',DB::raw('SUM(pp.total_liquidado) as total_planilla'))
							   ->groupBy('p.numero')
							   ->whereYear('p.fecha', '=', $year)
							   ->get();


		$diff_days = PlanillaEventual::select([
					    'numero',
					    DB::raw('DATEDIFF(fin_descarga, inicio_descarga)+1 as diff')
					])->get();
				              					   
        return response()->json(
        	['planillas' => $planillas,
        	 'diffs' => $diff_days]
        );
    }
}
