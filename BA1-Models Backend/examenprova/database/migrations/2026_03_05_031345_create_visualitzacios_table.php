<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();

            $table->boolean('completada')->default(false);
            $table->dateTime('data_inici');

            //  foreignId() crea una columna _id + constrained() afegeix la FK.
            //   constrained() infereix el nom de la taula a partir del nom de la columna:
            //   film_id → taula films, user_id → taula users.
            //   onDelete('cascade') fa que si s'esborra el film, s'esborren les views associades.
            $table->foreignId('film_id')->constrained('films')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
