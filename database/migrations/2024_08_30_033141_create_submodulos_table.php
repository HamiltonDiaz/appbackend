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
        Schema::create('submodulos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->unsignedBigInteger('id_modulos');
            $table->timestamps();

            //Llaves Foraneas
            $table->foreign('id_modulos')->references('id')->on('modulos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submodulos');
    }
};
