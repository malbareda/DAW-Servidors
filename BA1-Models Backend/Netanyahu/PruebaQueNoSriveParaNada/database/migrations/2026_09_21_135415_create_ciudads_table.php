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
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); //creo un campo de tipo string y que se llama nombre
            $table->integer('habitantes');
            $table->string('pais');
            $table->double('superficie');
            $table->string('alcalde');
            $table->boolean('amigo');
            $table->enum('estado', ['destruido','bombardeable','abandonado','perfecto','colonizable'])->default('colonizable');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudads');
    }
};
