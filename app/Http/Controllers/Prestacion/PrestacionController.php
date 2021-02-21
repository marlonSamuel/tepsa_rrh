<?php

namespace App\Http\Controllers\prestacion;

use App\Prestacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class PrestacionController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $prestacions = Prestacion::all();
        return $this->showAll($prestacions);
    }


    /**
     */
    public function store(Request $request)
    {
        $rules = [
            'descripcion' => 'required|string|unique:rrh.prestacions',
            'fijo' => 'required',
            'debito_o_credito'=>'required'
        ];

        $this->validate($request, $rules);
        $data = $request->all();

        $prestacion = Prestacion::create($data);

        return $this->showOne($prestacion, 201, 'insert');
    }

    /**
     */
    public function show(Prestacion $prestacion)
    {
        return $this->showOne($prestacion, 200, 'select');
    }

    /**
     */
    public function update(Request $request, Prestacion $prestacion)
    {
        $rules = [
            'descripcion' => 'required|string|unique:rrh.prestacions,descripcion,' . $prestacion->id,
        ];

        $this->validate($request, $rules);

        $prestacion->descripcion = $request->descripcion;
        $prestacion->fijo = $request->fijo;
        $prestacion->debito_o_credito = $request->debito_o_credito;

        if (!$prestacion->isDirty()) {
            return $this->errorResponse('se debe especificar al menos un valor para actualizar', 422);
        }

        $prestacion->save();

        return $this->showOne($prestacion, 201, 'update');
    }

    /**
     */
    public function destroy(Prestacion $prestacion)
    {
        $prestacion->delete();

        return $this->showOne($prestacion, 201, 'delete');
    }
}