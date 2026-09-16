<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administradors', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('departament');
            $table->timestamps();
        });

        //S'HA D'AFEGIR LA MIGRACIÓ DE LA TAULA INTERMITJA EN LA MIGRACIÓ QUE S'EXECUTI DESPRÉS DE LES DUES
        //TAMBÉ POTS CREAR UNA MIGARCIO TU DE LA TAULA INTERMITJA AMB php artisan make:migration create_administrador_film_table
        Schema::create('administrador_film', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administrador_id')->constrained('administradors')->onDelete('cascade');
            $table->foreignId('film_id')->constrained('films')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrators');
        Schema::dropIfExists('administrator_film');}
};
