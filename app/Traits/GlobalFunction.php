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
                    'afilacion_igss'=>' '.$value->empleado->igss,
                    'dpi'=>' '.$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta,
                    'puesto' => $cargo,
                    'turnos_trabajados'=>$value->total_turnos,
                    'costo_turnos'=>$value->total_monto_turnos,
                    'bono_turnos'=>$value->bono_turnos,
                    'septimo'=>$value->septimo,
                    'total_devengado' => $value->total_devengado
                ]);

                //merge info and turnos_cols to main data

                //push data to prestaciones_col
                foreach ($prestaciones as $p) {
                     $total_p = $value->prestaciones->where('prestacion_id',$p->id)->sum('total');
                     $p->descripcion = strtolower(str_replace(' ', '_', $p->descripcion));
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

                    $turnos_col["valor_turno_".$t->numero]=$valor_turno->salario;

                    $turnos_col["total_turno_".$t->numero]=$total_turno;
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
                    'afilacion_igss'=>' '.$value->empleado->igss,
                    'dpi'=>' '.$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta
                ]);

                //merge info and turnos_cols to main data
                $main_data = $info->merge($turnos_col);

                //calculate septimo
                $count_group = count($grouped);

                $main_data['bono_turno'] = $group->sum('bono_turno');

                $main_data['septimo'] = $value->septimo / $count_group;


                $main_data['total_devengado'] = $main_data['monto_turnos'] + $main_data['septimo'] + $main_data['bono_turno'];

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
                        $calculo = 0;
                        if(strtolower($p->descripcion) != "isr"){
                            $calculo = ($main_data['total_devengado'] * $p->calculo);
                        }
                    }

                    $desc = strtolower(str_replace(' ', '_', $p->descripcion));
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
    public function pagoCuentasYchequesDomo($fecha,$pagos)
    {

        $data = collect();
        $data2 = collect();
        foreach ($pagos as $key => $value) {

            if(!is_null($value->empleado->cuenta)){
                $info = collect([
                    'cuenta_origen'=>'902895911',
                    'cuenta_destino'=>$value->empleado->cuenta,
                    'fecha'=>date('d M Y', strtotime($fecha)),
                    'monto'=>$value->total,
                    'beneficiario'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                ]);
                $data->push($info);
            }else{
                $info = collect([
                    'fecha'=>date('d M Y', strtotime($fecha)),
                    'monto'=>$value->total,
                    'beneficiario'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                ]);

                $data2->push($info);
             
            }
        }

        return [$data,$data2];
    }

    public function pagoCuentasYchequesFijos($fecha,$pagos)
    {
        
        $data = collect();
        $data2 = collect();
        foreach ($pagos as $key => $value) {

            if(!is_null($value->empleado->cuenta)){
                $info = collect([
                    'cuenta_origen'=>'902895911',
                    'cuenta_destino'=>$value->empleado->cuenta,
                    'fecha'=>date('d M Y', strtotime($fecha)),
                    'monto'=>$value->total,
                    'beneficiario'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                ]);
                $data->push($info);
            }else{
                $info = collect([
                    'fecha'=>date('d M Y', strtotime($fecha)),
                    'monto'=>$value->total,
                    'beneficiario'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                ]);

                $data2->push($info);
             
            }
        }

        return [$data,$data2];
    }

    //get all payments data
    public function payrollFijo($pagos){
        //data collection to insert data
        $data = collect();

        //get prestacions


        foreach ($pagos as $key => $value) {
                //collection of dynamics columns to prestacions
                $prestaciones_col_cred = collect();
                $prestaciones_col_deb = collect();
                /*
                $cargo = '';
                $cargos = $value->detalle_pago->groupBy('cargo_turno.cargo.nombre');

                foreach ($cargos as $key => $c) {
                    $cargo = $cargo.', '.$key;
                }

                $cargo = substr($cargo,2); */
                //push general info to collection
                $info = collect([
                    'id'=>$value->id,
                    'codigo'=>$value->empleado_id,
                    'nombre'=>$value->empleado->primer_nombre.' '.$value->empleado->segundo_nombre.' '.$value->empleado->primer_apellido.' '.$value->empleado->segundo_apellido.' ',
                    'afilacion_igss'=>$value->empleado->igss,
                    'dpi'=>$value->empleado->dpi,
                    'cuenta'=>$value->empleado->cuenta,
                    'puesto' => $value->empleado->cargo->nombre,
                    'salario'=>$value->empleado->cargo->salario,
                ]);
               
                //merge info and turnos_cols to main data

                //push data to prestaciones_col
                $total_prestaciones = 0;
                $descuento_prestaciones = 0;
                foreach ($value->detalle_pago as $p) {
                    if (!$p->prestacion->debito_o_credito) {
                        $total_p = $p->prestacion->descripcion == 'bonificacion incetivo' ? $p->prestacion->calculo : $p->total;
                        $descripcion_p = str_replace(' ', '_', $p->prestacion->descripcion);
                        $prestaciones_col_cred[$descripcion_p] = $total_p;
                        $total_prestaciones += $total_p;
                    }else{
                       $total_p = $p->total;
                        $descripcion_p = str_replace(' ', '_', $p->prestacion->descripcion);
                        $prestaciones_col_deb[$descripcion_p] = $total_p;
                        $descuento_prestaciones += $total_p; 
                    }
                }
                if(!isset($prestaciones_col_cred['bonificacion_incetivo'])){
                    $prestaciones_col_cred['bonificacion_incetivo'] = 0;
                }
                if(!isset($prestaciones_col_deb['igss'])){
                    $prestaciones_col_deb['igss'] = 0;
                }
                if(!isset($prestaciones_col_deb['ISR'])){
                    $prestaciones_col_deb['ISR'] = 0;
                }
                 /*
                */
                //merge main data and prestaciones_col
                $info2 = $info->merge($prestaciones_col_cred);
                $info2['otro_ingreso']=$value->otro_ingreso;
                $info2['hora_extra_simple']=$value->hora_extra_simple;
                $info2['monto_hora_extra_simple']=$value->monto_hora_extra_simple;
                $info2['hora_extra_doble']=$value->hora_extra_doble;
                $info2['monto_hora_extra_doble']=$value->monto_hora_extra_doble;

                $total_ingresos = $value->empleado->cargo->salario +$total_prestaciones + $value->otro_ingreso + $value->monto_hora_extra_simple + $value->monto_hora_extra_doble ;
                $info2['total_ingresos'] = $total_ingresos;
                $info2['anticipo']= $value->quincena->fin_mes ? $value->anticipo : 0;
                $main_data = $info2->merge($prestaciones_col_deb);
                $main_data['otro_descuento'] = $value->otro_descuento;
                 
                $total_egresos = ($value->quincena->fin_mes ? $value->anticipo : 0) + $descuento_prestaciones + $value->otro_descuento;
                $main_data['total_egresos'] = $total_egresos;
                $main_data['liquido_a_recibir'] = $value->total; 
                $data->push($main_data);
                
        }
        return $data;

    }
}