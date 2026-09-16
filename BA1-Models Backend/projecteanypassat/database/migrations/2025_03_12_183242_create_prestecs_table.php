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
            $table->timestamps();
            $table->boolean("actiu"); //bool
            $table->datetime("data");//FECHA
            //EN UNA 1-N LAS LLAVES FORANEAS ESTAN DEL LADO DE LA N
            //CONSTRAINED = ASUME QUE HAY UNA TABLA LLAMADA LLIBRE CON UNA PK LLAMADA ID
            $table->foreignId("llibre_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->references("id")->on("users")->onDelete("cascade");

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
