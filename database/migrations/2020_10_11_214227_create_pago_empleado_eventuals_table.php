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
            $table->unsignedBigInteger('planilla_eventual_id');
            $table->unsignedBigInteger('empleado_id');
            $table->decimal('septimo',11,2)->default(0);
            $table->integer('total_turnos');
            $table->decimal('total_monto_turnos',11,2);
            $table->decimal('total_prestaciones',11,2);
            $table->decimal('total_devengado',11,2);
            $table->decimal('total_liquidado',11,2);
            $table->decimal('alimentacion',10,2)->default(0);
            $table->decimal('prestamos',10,2)->default(0);
            $table->decimal('otros_descuentos',10,2)->default(0);
            $table->decimal('descuento_prestaciones')->defualt(10,2);
            $table->boolean('confirmar_pago')->default(false);
            $table->timestamps();

            $table->foreign('planilla_eventual_id')->references('id')->on('planilla_eventuals')->onDelete('cascade');
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
