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
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo',200);
            $table->string('palabras_claves',200);
            $table->text('descripcion')->length(2600);
            $table->dateTime('fechainicio');
            $table->dateTime('fechafin');
            $table->text('ruta')->length(800);
            $table->unsignedBigInteger('id_categoria',);
            $table->timestamps();
             //Llaves Foraneas
             $table->foreign('id_categoria')->references('id')->on('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};