<?php

namespace App\Http\Controllers\Turno;

use App\Turno;
use App\Anio;
use App\Mes;
use App\Quincena;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class TurnoController extends ApiController
{
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
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

    public function start_quincena(){
        $now = Carbon::now();
        $anio = Anio::where('anio', $now->year)->first();
        $quincenas = Quincena::where('anio_id',$anio->id)->get();
        if (count($quincenas) > 0) {
            return $this->errorResponse('Quincenas ya han sido configuras para año en curso', 422);
        }else{
        $meses = Mes::all();
        $cont = 0;
        $contQuincena = 1;
        foreach ($meses as $key => $value) {
            for ($i = 1; $i < 3; $i++) {
                $dateInicio = Carbon::createFromFormat('m/Y', $value->id . '/' . $anio->anio)->firstOfMonth();
                $dateFin = Carbon::createFromFormat('m/Y', $value->id . '/' . $anio->anio)->firstOfMonth();
                $inicio = $i == 1 ? $dateInicio : $dateInicio->addDays(15);
                $fin = $i == 1 ? $dateFin->addDay(14) : $dateFin->lastOfMonth();
                $data = new Quincena;
                $data->quincena = $contQuincena;
                $data->fecha_inicio = $inicio;
                $data->fecha_fin = $fin;
                $data->anio_id = $anio->id;
                $data->mes_id = $value->id;
                $data->cerrada = false;
                $data->fin_mes = $i == 1 ? false : true;
                $data->valor_hora_extra_simple = 0;
                $data->valor_hora_extra_doble = 0;
                $data->save();
                $contQuincena++;
            }
        }
        return $this->showOne($data,201,'insert');
      }
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
