<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuincenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('quincenas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quincena');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedBigInteger('anio_id');
            $table->unsignedBigInteger('mes_id');
            $table->boolean('cerrada');
            $table->boolean('fin_mes');
            $table->decimal('valor_hora_extra_simple',11,2);
            $table->decimal('valor_hora_extra_doble',11,2);
            $table->timestamps();
            $table->foreign('anio_id')->references('id')->on('anios')->onDelete('cascade');
            $table->foreign('mes_id')->references('id')->on('meses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quincenas');
    }
}
