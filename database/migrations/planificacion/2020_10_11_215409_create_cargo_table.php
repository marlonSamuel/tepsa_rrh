<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('planificacion')->create('cargo', function (Blueprint $table) {
            $table->bigIncrements('idCargo')->autoIncrement();
            $table->string('nombre');
            $table->string('descripcion');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('planificacion')->dropIfExists('cargos');
    }
}
