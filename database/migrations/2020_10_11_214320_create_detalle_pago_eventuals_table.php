<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePagoEventualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('detalle_pago_eventuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asistencia_turno_bodega_id');
            $table->unsignedBigInteger('pago_empleado_eventual_id');
            $table->integer('conteo_turnos');
            $table->decimal('total',11,2);
            $table->timestamps();

            $table->foreign('asistencia_turno_bodega_id')->references('id')->on('asistencia_turno_bodegas');

            $table->foreign('pago_empleado_eventual_id')->references('id')->on('pago_empleado_eventuals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('rrh')->dropIfExists('detalle_pago_eventuals');
    }
}
