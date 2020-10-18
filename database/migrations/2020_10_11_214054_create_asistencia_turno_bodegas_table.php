<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsistenciaTurnoBodegasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('asistencia_turno_bodegas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('detalle_asignacion_empleado_id');
            $table->unsignedBigInteger('cargo_turno_id');
            $table->datetime('hora_entrada');
            $table->datetime('hora_salida')->nullable();
            $table->string('observaciones',255)->nullable();
            $table->integer('bodega');

            $table->timestamps();

            $table->foreign('detalle_asignacion_empleado_id')->references('id')->on('detalle_asignacion_empleados');
            $table->foreign('cargo_turno_id')->references('id')->on('cargo_turnos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('rrh')->dropIfExists('asistencia_turno_bodegas');
    }
}
