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
        Schema::create('bibliotecaris', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });

        //RECORDAD LA TABLA INTERMEDIA PONES LA TABLAS POR ORDEN ALFABETICO
        Schema::create('bibliotecari_llibre', function(Blueprint $table){
            $table->id();
            $table->foreignId('bibliotecari_id')->constrained('bibliotecaris')->cascadeOnDelete();
            $table->foreignId('llibre_id')->constrained('llibres')->cascadeOnDelete();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bibliotecaris');
    }
};
