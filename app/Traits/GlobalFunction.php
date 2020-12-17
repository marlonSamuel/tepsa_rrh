<?php
namespace App\Traits;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Prestacion;
use App\Turno;
use App\CargoTurno;

trait GlobalFunction
{
	//get all payments data
    public function payroll($pagos){
        //data collection to insert data
        $data = collect();

        //get prestacions
        $prestaciones = Prestacion::all();

        foreach ($pagos as $key => $value) {
                //collection of dynamics columns to prestacions
                $prestaciones_col = collect();

                $cargo = '';
                $cargos = $value->detalle_pago->groupBy('cargo_turno.cargo.nombre');

                foreach ($cargos as $key => $c) {
                    $cargo = $cargo.', '.$key;
                }

                $cargo = substr($cargo,2);

                //push general info to collection
                $info = collect([
                    'id'=>$value->id,
                    'codigo'=>$value->empleado_id,
                    'nombre'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                    'afilacion_igss'=>$value->empleado->igss,
                    'dpi'=>$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta,
                    'puesto' => $cargo,
                    'turnos_trabajados'=>$value->total_turnos,
                    'costo_turnos'=>$value->total_monto_turnos,
                    'septimo'=>$value->septimo,
                    'total_devengado' => $value->total_devengado
                ]);

                //merge info and turnos_cols to main data

                //push data to prestaciones_col
                foreach ($prestaciones as $p) {
                     $total_p = $value->prestaciones->where('prestacion_id',$p->id)->sum('total');
                     $p->descripcion = str_replace(' ', '_', $p->descripcion);
                     $prestaciones_col[$p->descripcion] = $total_p;
                }

                //merge main data and prestaciones_col
                $main_data = $info->merge($prestaciones_col);

                //total prestacions
                $main_data['total_prestaciones'] = $value->total_prestaciones;

                //dicounts
                $main_data['descuento_prestaciones'] = $value->descuento_prestaciones;

                $main_data['prestamos'] = $value->prestamos;
                $main_data['alimentos'] = $value->alimentacion;
                $main_data['otros_descuentos'] = $value->otros_descuentos;
                //calculate total page
                $main_data['liquido_a_recibir'] = $value->total_liquidado;

                //push data to data collection
                $data->push($main_data);
                
        }
        return $data;

    }

    //obtener array consolidado, reporte
    public function calculationMaster($pagos){
        //data collection to insert data
        $data = collect();
        //get turns
        $turnos = Turno::all();
        //get prestacions
        $prestaciones = Prestacion::all();
        //get cargo_turnos
        $cargo_turnos = CargoTurno::with('cargo')->get();

        foreach ($pagos as $key => $value) {
            //grouped data by cargo
            $grouped = $value->detalle_pago->groupBy('cargo_turno.cargo.nombre');

            foreach ($grouped as $key2 => $group) {
                //collection of dynamic columns for turns
                $turnos_col = collect();
                $total_turnos = 0;
                $monto_turnos = 0;
                //collection of dynamics columns to prestacions
                $prestaciones_col = collect();

                //push data to turnos_col collection
                foreach ($turnos as $t) {
                    $valor_turno = $cargo_turnos
                                    ->where('cargo.nombre',$key2)
                                    ->where('turno_id',$t->id)->first();
                    $conteo = $group->where('cargo_turno.turno_id',$t->id)->sum('conteo_turnos');
                    $total_turno = $group->where('cargo_turno.turno_id',$t->id)->sum('total');

                    $turnos_col["turno_".$t->numero]=$conteo;
                    $turnos_col["valor_".$t->numero]=$valor_turno->salario;
                    $turnos_col["total_".$t->numero]=$total_turno;
                    $total_turnos+= $conteo;
                    $monto_turnos+= $total_turno;
                }

                $turnos_col["total_turnos"]=$total_turnos;
                $turnos_col["monto_turnos"]=$monto_turnos;

                //recorrer los turnos y pagos por turno
                #$extra_cols =  $turnos_col->merge($prestaciones_col);

                #return $value->prestaciones;

                //push general info to collection
                $info = collect([
                    'id'=>$value->id,
                    'codigo'=>$value->empleado_id,
                    'nombre'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                    'puesto' => $key2,
                    'afilacion_igss'=>$value->empleado->afilacion_igss,
                    'dpi'=>$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta
                ]);

                //merge info and turnos_cols to main data
                $main_data = $info->merge($turnos_col);

                //calculate septimo
                $count_group = count($grouped);

                $main_data['septimo'] = $value->septimo / $count_group;

                $main_data['total_devengado'] = $main_data['monto_turnos'] + $main_data['septimo'];

                $total_prestaciones = 0;
                $descuento_prestaciones = 0;

                //push data to prestaciones_col
                foreach ($prestaciones as $p) {
                     if(!$p->fijo){
                        if($p->descripcion == 'bono 14' || $p->descripcion == 'aguinaldo'){
                            $calculo = ($main_data['total_devengado']*$main_data['total_turnos'])/(365 / $count_group);
                        }else{
                            $calculo = (($p->calculo/30)/24)*8*$main_data['total_turnos'];
                        }
                    }else{
                        $calculo = ($main_data['total_devengado'] * $p->calculo);
                    }

                    $desc = str_replace(' ', '_', $p->descripcion);
                    $prestaciones_col[$desc]=$calculo;

                    #calculo de credito o debito de prestaciones
                    if($p->debito_o_credito){
                        $descuento_prestaciones += $calculo;
                    }else{
                        $total_prestaciones+= $calculo;
                    }
                }

                //merge main data and prestaciones_col
                $main_data = $main_data->merge($prestaciones_col);

                //total prestacions
                $main_data['total_prestaciones'] = $total_prestaciones;

                //dicounts
                $main_data['descuento_prestaciones'] = $descuento_prestaciones;

                $main_data['prestamos'] = $value->prestamos / $count_group;
                $main_data['alimentos'] = $value->alimentacion / $count_group;
                $main_data['otros_descuentos'] = $value->otros_descuentos / $count_group;
                //calculate total page
                $main_data['liquido_a_recibir'] = $main_data['total_devengado'] + $main_data['total_prestaciones'] - $main_data['descuento_prestaciones'] - $main_data['prestamos'] - $main_data['alimentos'] - $main_data['otros_descuentos'];

                //push data to data collection
                $data->push($main_data);
            }
        }
        return $data;
    }

    public function pagoCuentasYcheques($fecha,$pagos)
    {
        $data = collect();
        $data2 = collect();
        foreach ($pagos as $key => $value) {

            if(!is_null($value->empleado->cuenta)){
                $info = collect([
                    'cuenta_origen'=>'902895911',
                    'cuenta_destino'=>$value->empleado->cuenta,
                    'fecha'=>date('d M Y', strtotime($fecha)),
                    'monto'=>$value->total_liquidado,
                    'beneficiario'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                ]);
                $data->push($info);
            }else{
                $info = collect([
                    'fecha'=>date('d M Y', strtotime($fecha)),
                    'monto'=>$value->total_liquidado,
                    'beneficiario'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                ]);

                $data2->push($info);
             
            }
        }

        return [$data,$data2];
    }
}