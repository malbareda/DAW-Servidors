<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id(); // Autonumeric i va que tira

            $table->string('titol');

            //enum, ja sabem com funciona
            $table->enum('genere', ['ACCIO', 'COMEDIA', 'DRAMA']);

            $table->integer('durada'); // en minuts

            $table->timestamps(); //Afegeix created_at i updated_at automàticament
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
