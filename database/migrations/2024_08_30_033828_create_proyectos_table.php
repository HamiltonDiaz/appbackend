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
            $table->json('palabras_claves');
            // $table->string('palabras_claves',200);
            $table->text('descripcion')->length(2600);
            $table->date('fechainicio');
            $table->date('fechafin')->nullable();
            $table->text('ruta')->length(800)->nullable();
            $table->unsignedBigInteger('id_categoria',);
            $table->unsignedBigInteger('id_estado');
            $table->timestamps();
             //Llaves Foraneas
             $table->foreign('id_categoria')->references('id')->on('categoria');
             $table->foreign('id_estado')->references('id')->on('estados');

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
