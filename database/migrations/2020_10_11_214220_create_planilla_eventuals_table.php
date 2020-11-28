<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanillaEventualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planilla_eventuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asignacion_empleado_id');
            $table->string('buque',25);
            $table->string('numero',15);
            $table->date('inicio_descarga');
            $table->date('fin_descarga');
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('asignacion_empleado_id')->references('id')->on('asignacion_empleados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planilla_eventuals');
    }
}
