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
        Schema::create('ejercitos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('soldados')->default(0);
            //FOREIGNID
            //PONES FOREIGNID( NOMBRE DE LA FK) CONSTRAINED (TABLA A LA QUE REFERENCIA) ONDELETE (QUE PASA CUANDO BORRAS EL OTRO LADO DE LA RELACION)
            $table->foreignId('ciudad_id')->constrained('ciudades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejercitos');
    }
};
