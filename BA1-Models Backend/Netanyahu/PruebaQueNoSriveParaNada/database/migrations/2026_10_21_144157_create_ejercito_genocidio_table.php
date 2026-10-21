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
        //Laravel espera las tablas intermedias en orden alfabetico
        Schema::create('ejercito_genocidio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ejercito_id')->constrained('ejercitos');
            $table->foreignId('genocidio_id')->constrained('genocidios');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genocidio_ejercito');
    }
};
