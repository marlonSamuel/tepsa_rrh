<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagoEmpleadoFijosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('rrh')->create('pago_empleado_fijos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('quincena_id');
            $table->unsignedBigInteger('cargo_id');
            $table->decimal('total',11,2);
            $table->decimal('otro_descuento',11,2);
            $table->decimal('anticipo',11,2);
            $table->decimal('otro_ingreso',11,2);
            $table->decimal('hora_extra_simple',11,2);
            $table->decimal('hora_extra_doble',11,2);
            $table->decimal('monto_hora_extra_simple',11,2);
            $table->decimal('monto_hora_extra_doble',11,2);
            $table->timestamps();
            $table->foreign('quincena_id')->references('id')->on('quincenas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_empleado_fijos');
    }
}
