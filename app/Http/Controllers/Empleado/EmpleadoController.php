<?php

namespace App\Http\Controllers\Empleado;

use App\Empleado;
use App\Carnet;
use App\CarnetEmpleado;
use App\EmpleadoPrestacion;
use App\DetalleAsignacionEmpleado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        
        $empleados = Empleado::with('cargo')->get();

        return $this->showAll($empleados);
    }
//obtener prestaciones e id  empleado
    public function show($idEmpleado){

        $detalle = Empleado::where('idEmpleado',$idEmpleado)->with('empleado_prestacion.prestacion')->get();

        return $this->showAll($detalle);
    }
   
    /**
     */

    public function store(Request $request)
    {
        $rules = [
            'dpi' => 'required|string',
            'nit' => 'required',
            'primer_nombre' => 'required|string',
            'primer_apellido' => 'required|string'
        ];

        $this->validate($request, $rules);
        $data = $request->all();


        $imagePath = '';
        if ($request->foto != null || $request->foto != '') {
            if (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                $data_img = substr($request->foto, strpos($request->foto, ',') + 1);
                $data_img = base64_decode($data_img);

                $imagePath = $request->dpi . '_' . time() . '.png';
                Storage::disk('images')->put($imagePath, $data_img);
            }
            
            $data['foto'] = 'img/fotos/' . $imagePath;
            
        }

        
        if ($request->tipo_empleado == '1') {
            $db = DB::connection('rrh');
            $db->beginTransaction();
                $empleado = Empleado::create($data);
                
                //dd($empleado);
                $model = new CarnetEmpleado;
                $model->empleado_id = $empleado->idEmpleado;
                $model->carnet_id = $request->carnet_id;
                $model->save();

                $carnet = Carnet::find($request->carnet_id);
                $carnet->asignado = true;
                $carnet->save();
            $db->commit();    
        }else{
            $empleado = Empleado::create($data);             
        }

        
        return $this->showOne($empleado, 201, 'insert');
    }


    public function update(Request $request, Empleado $empleado)
    {

        $rules = [
            'dpi' => 'required|string',
            'nit' => 'required',
            'primer_nombre' => 'required|string',
            'primer_apellido' => 'required|string'
        ];
        
        $this->validate($request, $rules);

        $empleado->dpi = $request->dpi;
        $empleado->nit = $request->nit;
        $empleado->primer_nombre = $request->primer_nombre;
        $empleado->segundo_nombre = $request->segundo_nombre;
        $empleado->primer_apellido = $request->primer_apellido;
        $empleado->segundo_apellido = $request->segundo_apellido;
        $empleado->telefono = $request->telefono;
        $empleado->direccion = $request->direccion;
        $empleado->estado = $request->estado;
        $empleado->tipo_empleado = $request->tipo_empleado;
        $empleado->cuenta = $request->cuenta;
        $empleado->idCargo = $request->idCargo;
        $empleado->estado = $request->estado;
        $empleado->igss = $request->igss;

        //$employment->idEmpleado = $request->idEmpleado;

        if($request->foto != null || $request->foto != ''){
            $imagePath = '';
            if (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                $data = substr($request->foto, strpos($request->foto, ',') + 1);
                $data = base64_decode($data);
                $imagePath = $request->dpi.'_'.time().'.png';
                Storage::disk('images')->put($imagePath, $data);
            }
            $empleado->foto = 'img/fotos/'.$imagePath;
        }

        if (!$empleado->isDirty()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }
        
        $empleado->save();
        return $this->showOne($empleado);
    }
    public function destroy($id)
    {
        $detalle_asignacion = DetalleAsignacionEmpleado::where('empleado_id',$id)->get();
        //valida asignaciones
        if (count($detalle_asignacion) == 0) {

            $empleado = Empleado::find($id);
        if ($empleado->tipo_empleado == 1) {
            $db = DB::connection('rrh');
            $db->beginTransaction(); 
            
            
            $empleado_prestacion = EmpleadoPrestacion::where('empleado_id',$id)->get();
            if (count($empleado_prestacion) > 0) {
                foreach ($empleado_prestacion as $item) {
                    $item->delete();
                }
            }
                                   
            $carnet_empleado = CarnetEmpleado::where('empleado_id',$id)->first();
            if ($carnet_empleado != null) {
                $carnet = Carnet::find($carnet_empleado->carnet_id);
                      $carnet->asignado = false;
                      $carnet->save();

                $carnet_empleado->delete();  
            }    
            $empleado->delete();
            $db->commit();

        }else{
            $empleado_prestacion = EmpleadoPrestacion::where('empleado_id',$id)->get();
            if (count($empleado_prestacion) > 0) {
                foreach ($empleado_prestacion as $item) {
                    $item->delete();
                }
            }

           $empleado->delete(); 
        }
            return $this->showOne($empleado,201,'delete');
            
        }else{
            
           return $this->errorResponse('No se puede eliminar de forma permamente el recurso porque está relacionado con algún otro.',409);
        } 
        
    }

    public function disabledEmpleado($idEmpleado){
        $empleado = Empleado::find($idEmpleado);
        if ($empleado->tipo_empleado == 1) {
            $db = DB::connection('rrh');
            $db->beginTransaction();
            $carnet_empleado = CarnetEmpleado::where('empleado_id',$idEmpleado)->first();
            if ($carnet_empleado != null) {
                $carnet = Carnet::find($carnet_empleado->carnet_id);
                      $carnet->asignado = false;
                      $carnet->save();
                $carnet_empleado->delete();  
            }
            $empleado->estado = $empleado->estado == 'I'?'A':'I';   
            $empleado->save();
            $db->commit(); 
        }else{
            $empleado->estado = $empleado->estado == 'I'?'A':'I';
            $empleado->save();
        }

        return $this->showOne($empleado,201,'update');
    }

    public function empleado_carnet($id){
        $carnet_empleado = CarnetEmpleado::where('carnet_id',$id)->first();
        if ($carnet_empleado != null) {
            $empleado = Empleado::where('idEmpleado',$carnet_empleado->empleado_id)->first();

            return $this->showOne($empleado,200,'select');
        }else{
            return $this->errorResponse("false", 404);
        }

    }

}