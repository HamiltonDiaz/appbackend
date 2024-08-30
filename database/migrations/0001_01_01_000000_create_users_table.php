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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('primer_nombre',100);
            $table->string('otros_nombres',100);
            $table->string('primer_apellido',100);
            $table->string('segundo_apellido',100);
            $table->string('email',100)->unique();
            $table->string('nombre_usuario',20);
            $table->string('telefono',20);
            $table->string('numero_identificacion',20);
            $table->string('name');            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('id_tipos_identificacion');
            // $table->unsignedBigInteger('id_rol');
            $table->unsignedBigInteger('id_estado');
            $table->rememberToken();
            $table->timestamps();

            //Llaves Foraneas
            $table->foreign('id_tipos_identificacion')->references('id')->on('tipos_identificacion');
            $table->foreign('id_estado')->references('id')->on('estados');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};