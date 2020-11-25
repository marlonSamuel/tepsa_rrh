<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePagoEventualsTable extends Migration
{
    /**
    
     */
    public function up()
    {
        Schema::connection('rrh')->create('detalle_pago_eventuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cargo_turno_id');
            $table->unsignedBigInteger('pago_empleado_eventual_id');
            $table->integer('conteo_turnos');
            $table->decimal('valor_turno',6,2);
            $table->decimal('total',11,2);
            $table->timestamps();

            $table->foreign('cargo_turno_id')->references('id')->on('cargo_turnos');

            $table->foreign('pago_empleado_eventual_id')->references('id')->on('pago_empleado_eventuals')->onDelete('cascade');
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
