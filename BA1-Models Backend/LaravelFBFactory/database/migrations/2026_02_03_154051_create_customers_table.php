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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nom');
            $table->string('dni')->unique();
            $table->boolean('activo');
            $table->foreignId('factory_id')->constrained('factories');
            // Afegim la clau forana a users
            // nullable() permet que hi hagi customers sense user assignat (opcional)
            // Si vols que sigui obligatori, treu el nullable()
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            // Si vols que sigui única (1 user = 1 customer), descomenta la següent línia:
            // $table->unique('user_id');
        });

        //S'HA D'AFEGIR LA MIGRACIÓ DE LA TAULA INTERMITJA EN LA MIGRACIÓ QUE S'EXECUTI DESPRÉS DE LES DUES
        //TAMBÉ POTS CREAR UNA MIGARCIO TU DE LA TAULA INTERMITJA AMB php artisan make:migration create_customer_femboy_table
        Schema::create('customer_femboy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('femboy_id')->constrained('femboys')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
