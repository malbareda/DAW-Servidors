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
            $table->string("nom");
            $table->timestamps();
        });

        Schema::create('bibliotecari_llibre', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bibliotecari_id")->constrained()->onDelete('cascade');
            $table->foreignId("llibre_id")->constrained()->onDelete('cascade');
            $table->timestamps();
        });



    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bibliotecaris');
        Schema::dropIfExists('bibliotecari_llibre');

    }
};
