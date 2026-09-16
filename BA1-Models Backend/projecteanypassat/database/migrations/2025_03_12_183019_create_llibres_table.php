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
        Schema::create('llibres', function (Blueprint $table) {
            //crea llave primaria autonumerica
            $table->id();
            $table->integer("llibre_id")->constrained()->onDelete("cascade");
            //crea timestamps. Cuando haces eso crea columnas de timestamps automaticas que se editan automaticamente con el last_created y last_modified
            //ves, eso esta bien
            $table->timestamps();
            //defino titol como string, varchar
            $table->string("titol");//VARCHAR
            //defino categoria como un enum
            $table->enum("categoria",["FICCIO","NOFICCIO"]); //ENUM
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llibres');
    }
};
