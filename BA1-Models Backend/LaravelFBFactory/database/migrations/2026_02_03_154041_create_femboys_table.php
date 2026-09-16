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
        Schema::create('femboys', function (Blueprint $table) {

            //$table->id() crea: - Una columna 'id'- De tipus BIGINT UNSIGNED- AUTO_INCREMENT- PRIMARY KEY

            $table->id();
            //varchar 255 per defecte
            $table->string('nombre');
            $table->string('procedencia');
            //crea una date, també datetime, timestamp i time
            $table->date('fecha_cumpleanos');
            $table->string('descripcion');
            //enum
            $table->enum('estado', ['Disponible', 'Reservado', 'No Disponible'])->default('Disponible');
            //clauforana. El references es la columna i el on la taula que referencia.
            //tambe podries fer la versió curta
            //$table->foreignId('factory_id')->constrained();
            //això automàticament busca la taula factories (plural del nom sense id, referencia id, i afegeix un index)
            $table->foreignId('factory_id')->references('id')->on('factories');
            //timestamps automatics. Crea 2, created_at i updated_at. Laravels els omple automaticament
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('femboys');
    }
};

/**
 * ==========================================
 * ALTRES TIPUS DE CAMPS ÚTILS
 * ==========================================
 *
 * // TEXT (més gran que string)
 * $table->text('descripcio_llarga');
 *
 * // BOOLEAN
 * $table->boolean('actiu')->default(true);
 *
 * // DECIMAL (per preus, etc.)
 * $table->decimal('preu', 8, 2);  // 8 dígits totals, 2 decimals
 *
 * // JSON
 * $table->json('metadades');
 *
 * // NULLABLE (pot ser NULL)
 * $table->string('telefon')->nullable();
 *
 * // UNIQUE (valor únic)
 * $table->string('email')->unique();
 *
 * // INDEX (per cerques ràpides)
 * $table->string('nom')->index();
 *
 * // SOFT DELETES (esborrat lògic)
 * $table->softDeletes();  // Crea deleted_at
 * */
