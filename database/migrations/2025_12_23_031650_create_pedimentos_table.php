<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('pedimentos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_proveedor');
            $table->bigInteger('id_planta');
            $table->string('numero_pedimento');
            $table->string('division');
            $table->string('periodo');
            $table->integer('avance');
            $table->string('estatus');
            $table->string('responsable');
            $table->string('procesos');
            $table->date('inicio_proceso');
            $table->string('tipo');
            $table->timestamps();
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedimentos');
    }
};
