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
        Schema::create('ponis', function (Blueprint $table) {
            $table->id(); //PK
            $table->string('nom');
            $table->integer('edat');
            $table->enum('mida',['PETIT','MITJA','GRAN']);
            //FK NULLABLE: un poni pot no tenir propietari (es de l'hipica)
            //nullOnDelete: si s'esborra l'usuari el poni no s'esborra, es queda sense propietari
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ponis');
    }
};
