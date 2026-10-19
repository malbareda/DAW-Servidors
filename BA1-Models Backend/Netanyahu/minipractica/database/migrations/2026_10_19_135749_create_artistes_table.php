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
        //LARAVEL LA LLAMARIA 'artistas' (PLURALIZA EN INGLES)
        //YO LE FUERZO EL NOMBRE EN CATALAN, Y EN EL MODELO PONGO EL $table
        Schema::create('artistes', function (Blueprint $table) {
            $table->id(); //id() ES LO MISMO QUE bigIncrements('id')
            $table->string('nom'); //por defecto los campos son NOT NULL
            $table->string('pais');
            $table->text('bio')->nullable(); //text para textos largos, nullable para que pueda estar vacio
            $table->string('imatge_url')->nullable();
            $table->boolean('actiu')->default(true); //si no le paso nada, vale true

            $table->timestamps(); //created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artistes');
    }
};
