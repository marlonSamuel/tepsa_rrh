<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleAsignacionEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('detalle_asignacion_empleados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('turno_id');
            $table->unsignedBigInteger('asignacion_empleado_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('carnet_id');
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('turno_id')->references('id')->on('turnos');

            $table->foreign('asignacion_empleado_id')->references('id')->on('asignacion_empleados')->onDelete('cascade');

            $table->foreign('carnet_id')->references('id')->on('carnets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('rrh')->dropIfExists('detalle_asignacion_empleados');
    }
}
