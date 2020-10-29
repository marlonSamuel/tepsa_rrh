<?php

namespace App\Http\Controllers\carnet;

use App\Carnet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class CarnetController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $carnets = Carnet::all();
        return $this->showAll($carnets);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'codigo' => 'required|string|unique:rrh.carnets'
        ];

        $this->validate($request,$rules);
        $data = $request->all();

        $carnet = Carnet::create($data);

        return $this->showOne($carnet,201,'insert');
    }

    /**
     */
    public function show(Carnet $carnet)
    {
        return $this->showOne($carnet,200,'select');
    }

    /**
     */
    public function update(Request $request, Carnet $carnet)
    {
        $rules = [
            'codigo' => 'required|string|unique:rrh.carnets,codigo,' . $carnet->id,
        ];

        $this->validate($request,$rules);

        $carnet->codigo = $request->codigo;

        if(!$carnet->isDirty())
        {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar',422);
        }

        $carnet->save();

        return $this->showOne($carnet,201,'update');

    }

    /**
     */
    public function destroy(Carnet $carnet)
    {
        $carnet->delete();

        return $this->showOne($carnet,201,'delete');
    }
}
