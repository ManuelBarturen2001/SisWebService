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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('proveedor'); // reniec o migraciones
            $table->unsignedBigInteger('credencial_id'); // ID de la credencial usada
            $table->string('documento_consultado'); // DNI o documento consultado
            $table->boolean('exitoso'); // Si la consulta fue exitosa
            $table->string('codigo_respuesta')->nullable(); // Código de respuesta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            //
        });
    }
};
