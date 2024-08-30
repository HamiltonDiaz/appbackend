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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->dateTime('fecha_modificacion');
            $table->unsignedBigInteger('id_proyecto',);
            $table->unsignedBigInteger('id_estado',);
            $table->timestamps();

             //Llaves Foraneas
             $table->foreign('id_proyecto')->references('id')->on('proyectos');
             $table->foreign('id_estado')->references('id')->on('estados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
