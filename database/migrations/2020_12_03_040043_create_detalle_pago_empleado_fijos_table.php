<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePagoEmpleadoFijosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pago_empleado_fijos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pago_empleado_fijo_id');
            $table->unsignedBigInteger('prestacion_id');
            $table->decimal('total',11,2);
            $table->timestamps();
            $table->foreign('pago_empleado_fijo_id')->references('id')->on('pago_empleado_fijos')->onDelete('cascade');
            $table->foreign('prestacion_id')->references('id')->on('prestacions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_pago_empleado_fijos');
    }
}
