<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsistenciaDomosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencia_domos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asignacion_domo_id');
            $table->datetime('hora_entrada')->nullable();
            $table->datetime('hora_salida')->nullable();
            $table->smallInteger('turno');
            $table->string('observaciones',255)->nullable();
            $table->boolean('bloqueado')->default(false);
            $table->boolean('desbloqueado')->default(false);
            $table->string('razon_desbloqueo',250)->nullable();

            $table->timestamps();

            $table->foreign('asignacion_domo_id')->references('id')->on('asignacion_domos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asistencia_domos');
    }
}
