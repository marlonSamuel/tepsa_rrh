<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsignacionDomosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignacion_domos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asignacion_empleado_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('carnet_id');
            $table->unsignedBigInteger('cargo_id');
            $table->date('fecha');
            $table->timestamps();

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
        Schema::dropIfExists('asignacion_domos');
    }
}
