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
        Schema::create('prestecs', function (Blueprint $table) {
            $table->id();
            $table->boolean('actiu');
            $table->dateTime('data'); //datetime fecha y hora
            //FOREIGNID
            //PONES FOREIGNID (NOMBRE DE LA FK) CONSTRAINED (TABLA A LA QUE HACE REFERENCIA) CASCADEONDELETE (SI TIENE DELETE EN CASCADA)
            //EN ESTE CASO EN CONCRETO LAS 2 FK VIENE AQUI PORQUE ES LA ESTRUCTURA DE LA TABLA
            //ESO NO SIGNFICA QUE EN EL EXAMEN REAL TAMBIEN ESTEN LAS DOS FK EN LA MISMA TABLA
            $table->foreignId('llibre_id')->constrained('llibres')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestecs');
    }
};
