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
        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            //FOREIGNID
            //PONES FOREIGNID( NOMBRE DE LA FK) CONSTRAINED (TABLA A LA QUE REFERENCIA) CASCADEONDELETE (SI BORRO EL ARTISTA, SE BORRAN SUS CONCIERTOS)
            //como la tabla no se llama 'artistas' le tengo que decir el nombre a mano
            $table->foreignId('artista_id')->constrained('artistes')->cascadeOnDelete();
            $table->string('nom'); //nombre del concierto o gira
            $table->dateTime('data_hora'); //fecha Y hora
            $table->unsignedInteger('durada_minuts'); //unsigned = no puede ser negativo
            //ENUM: solo puede valer uno de estos valores
            $table->enum('tipus', ['CONCERT', 'DJ_SET', 'SHOWCASE', 'ACUSTIC']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concerts');
    }
};
