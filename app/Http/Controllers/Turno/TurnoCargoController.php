<?php

namespace App\Http\Controllers\Turno;

use App\Turno;
use App\CargoTurno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class TurnoCargoController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }
    public function index(Turno $turno){
        $cargos = $turno->cargo_turnos()->with('cargo')->get();
        return $this->showAll($cargos);
    }

    public function store(Request $request){

    	foreach ($request->cargos as $item) {            
            if ($item['id'] != null || $item['id'] > 0 ) {
                $id = $item['id'];
                $cargo_turno = CargoTurno::find($id);
                $cargo_turno->cargo_id = $item['cargo_id'];
                $cargo_turno->turno_id = $request->turno_id;
                $cargo_turno->salario = $item['salario'];
                $cargo_turno->salario_hora = 0;
                $cargo_turno->save();
            }else{
                $cargo_turno = new CargoTurno();
                $cargo_turno->cargo_id = $item['cargo_id'];
                $cargo_turno->turno_id = $request->turno_id;
                $cargo_turno->salario = $item['salario'];
                $cargo_turno->salario_hora = 0;
                $cargo_turno->save();  
            }
    		
    	}

    	return $this->showOne($cargo_turno,201,'insert');
    }
}
