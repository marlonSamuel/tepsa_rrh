<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagoEmpleadoDomosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_empleado_domos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planilla_eventual_id');
            $table->unsignedBigInteger('asistencia_domo_id');
            $table->integer('conteo_turno');
            $table->decimal('total',11,2);
            $table->boolean('confirmar_pago');
            $table->timestamps();
        });
        $table->foreign('planilla_eventual_id')->references('id')->on('planilla_eventuals')->onDelete('cascade');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_empleado_domos');
    }
}
