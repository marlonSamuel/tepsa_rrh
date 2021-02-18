<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsistenciaAlmuerzosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('asistencia_almuerzos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('detalle_asignacion_empleado_id');
            $table->char('tipo_alimento',1);
            $table->timestamps();

            $table->foreign('detalle_asignacion_empleado_id')->references('id')->on('detalle_asignacion_empleados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('rrh')->dropIfExists('asistencia_almuerzos');
    }
}
