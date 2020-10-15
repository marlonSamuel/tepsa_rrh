<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanoEstibaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('planificacion')->create('plano_estiba', function (Blueprint $table) {
            $table->bigIncrements('idPlano_Estiba');
            $table->integer('no_importacion');
            $table->decimal('peso_total');
            $table->char('estado',1);
            $table->unsignedBigInteger('idBuque');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('planificacion')->dropIfExists('plano_estiba');
    }
}
