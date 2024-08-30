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
        Schema::create('historico', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',255);
            $table->dateTime('fecha');
            $table->unsignedBigInteger('id_proyecto',);
            $table->unsignedBigInteger('id_usuario',);
            $table->timestamps();

             //Llaves Foraneas
             $table->foreign('id_proyecto')->references('id')->on('proyectos');
             $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico');
    }
};
