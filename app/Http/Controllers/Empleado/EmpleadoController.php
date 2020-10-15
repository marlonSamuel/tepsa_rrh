<?php

namespace App\Http\Controllers\Empleado;

use App\Empleado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class EmpleadoController extends ApiController
{
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
    	
        $empleados = Empleado::with('cargo')->get();

        return $this->showAll($empleados);
    }

    /**
     */
    
    public function store(Request $request)
    {
        $rules = [
            'dpi' => 'required|string|unique:planificacion.empleado',
            'nit' => 'required',
            'primer_nombre' => 'required|string',
            'primer_apellido' => 'required|string'
        ];

        $this->validate($request, $rules);
        $data = new Empleado();
        $data->idEmpleado = $request->idEmpleado;
        $data->primer_nombre = $request->primer_nombre;
        $data->segundo_nombre = $request->segundo_nombre;
        $data->primer_apellido = $request->primer_apellido;
        $data->segundo_apellido = $request->segundo_apellido;
        $data->dpi = $request->dpi;
        $data->nit = $request->nit;
        $data->direccion = $request->direccion;
        $data->telefono = $request->telefono;
        $data->idCargo = $request->idCargo;
        $data->foto = $request->foto;
        $data->cuenta = $request->cuenta;
        $data->tipo_empleado = $request->tipo_empleado;
        
        
        $imagePath = '';
            if($request->foto != null || $request->foto != ''){
                if (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                    $data_img = substr($request->foto, strpos($request->foto, ',') + 1);
                    $data_img = base64_decode($data_img);
                    $imagePath = $request->codigo.'_'.time().'.png';
                    Storage::disk('images')->put($imagePath, $data_img);
                }

                $data['foto'] = 'img/fotos/'.$imagePath;
            }

        $empleado = Empleado::create($data);
            

        return $this->showOne($empleado, 201, 'insert');
    }
}
