<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagoEmpleadoEventualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('pago_empleado_eventuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asignacion_empleado_id');
            $table->unsignedBigInteger('empleado_id');
            $table->decimal('total',11,2);
            $table->decimal('alimentacion',10,2);
            $table->decimal('prestamos',10,2);
            $table->decimal('otrosDescuentos',10,2);
            $table->boolean('confirmarPago');
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
        Schema::connection('rrh')->dropIfExists('pago_empleado_eventuals');
    }
}
