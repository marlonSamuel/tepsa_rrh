<?php

namespace App\Http\Controllers\Cargo;

use App\Cargo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class CargoController extends ApiController
{
   public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $empleados = Cargo::with('empleados')->get();
        return $this->showAll($empleados);
    }

    public function store(Request $request){
    	$rules = ['nombre'=>'required|string',
    			   'descripcion'=>'required|string',
    			   'estado'=>'required'];

    	$this->validate($request, $rules);
        $data = $request->all();

        $cargo = Cargo::create($data);

        return $this->showOne($cargo,201,'insert');


    }

    public function show(Cargo $cargo){

    	return $this->showOne($cargo,200,'select');

    }
    public function update(Request $request, Cargo $cargo){
    	 $rules = ['nombre'=>'required|string',
    			   'descripcion'=>'required|string'];

        $this->validate($request, $rules);

        $cargo->descripcion = $request->descripcion;
        $cargo->nombre = $request->nombre;
        $cargo->salario = $request->salario;

        if (!$cargo->isDirty()) {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar', 422);
        }

        $cargo->save();

        return $this->showOne($cargo, 201, 'update');

    }

    public function destroy(Cargo $cargo){
    	$cargo->delete();
    	return $this->showOne($cargo,201,'delete');

    }
    public function disable($idCargo){
    	$cargo = Cargo::find($idCargo);
    	$cargo->estado = $cargo->estado == 'I'?'A':'I';
    	$cargo->save();
    	return $this->showOne($cargo,201,'update');

    }
}
