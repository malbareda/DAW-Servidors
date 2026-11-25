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
        //TAULA INTERMITJA N-N: noms de les taules per ordre alfabetic i en singular
        Schema::create('concurs_poni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concurs_id')->constrained('concursos')->cascadeOnDelete();
            $table->foreignId('poni_id')->constrained('ponis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concurs_poni');
    }
};
