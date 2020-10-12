<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('planificacion')->create('empleado', function (Blueprint $table) {
            $table->bigIncrement('idEmpleado')->autoIncrement();
            $table->string('nit');
            $table->string('dpi');
            $table->string('primer_nombre');
            $table->string('segundo_nombre');
            $table->string('primer_apellido');
            $table->string('segundo_apellido');
            $table->string('direccion');
            $table->string('telefono');
            $table->unsignedBigInteger('idCargo');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->foreign('idCargo')->references('idCargo')->on('cargos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('rrh')->dropIfExists('empleados');
    }
}
