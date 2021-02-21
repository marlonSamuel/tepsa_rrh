<?php

namespace App\Http\Controllers\Turno;

use App\Turno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class TurnoController extends ApiController
{
   public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $turnos = Turno::all();
        return $this->showAll($turnos);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'numero'=>'required|integer|unique:rrh.turnos',
            'hora_inicio' => 'required',
            'hora_fin' => 'required'
        ];

        $this->validate($request,$rules);
        $data = $request->all();

        $turno = Turno::create($data);

        return $this->showOne($turno,201,'insert');
    }

    /**
     */
    public function show(Turno $turno)
    {
        return $this->showOne($turno,200,'select');
    }

    /**
     */
    public function update(Request $request, Turno $turno)
    {
        $rules = [
            'numero' => 'required|integer|unique:rrh.turnos,numero,' . $turno->id,
            'hora_inicio' => 'required',
            'hora_fin' => 'required'
        ];


        $this->validate($request,$rules);

        $turno->numero = $request->numero;
        $turno->hora_inicio = $request->hora_inicio;
        $turno->hora_fin = $request->hora_fin;



        if(!$turno->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $turno->save();

        return $this->showOne($turno,201,'update');

    }

    /**
     */
    public function destroy(Turno $turno)
    {
        $turno->delete();

        return $this->showOne($turno,201,'delete');
    }
}
