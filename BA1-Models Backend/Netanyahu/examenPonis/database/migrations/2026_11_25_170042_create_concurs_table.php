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
        //"concurs" en plural no es regular, aixi que poso el nom de la taula a ma (i al model tambe)
        Schema::create('concursos', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->date('data'); //date: nomes la data, sense hora
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concursos');
    }
};
