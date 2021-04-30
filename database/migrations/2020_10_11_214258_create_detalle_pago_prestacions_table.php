<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePagoPrestacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('detalle_pago_prestacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prestacion_id');
            $table->unsignedBigInteger('pago_empleado_eventual_id');
            $table->decimal('total',11,5);
            $table->timestamps();

            $table->foreign('prestacion_id')->references('id')->on('prestacions');

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
        Schema::connection('rrh')->dropIfExists('detalle_pago_prestacions');
    }
}
