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
        Schema::create('usuarios_has_proyectos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario',);
            $table->unsignedBigInteger('id_proyecto');
            $table->timestamps();
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_has_proyectos');
    }
};
