<?php

namespace App\Models;

use App\Enums\EstadoFemboy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Femboy extends Model
{

    use HasFactory;
    /**
     * NOM DE LA TAULA
     *
     * Per defecte Laravel fa servir el plural del nom del model en snake_case
     * Model: Femboy → Taula: femboys (automàtic)
     *
     * Si vols un nom personalitzat, ho especifiques aquí:
     */
    protected $table = 'femboys';

    /**
     * CLAU PRIMÀRIA
     *
     * Per defecte Laravel assumeix que la PK es diu 'id'
     * Si la teva PK es diu diferent, ho especifiques aquí
     *
     * Equivalent Django: No cal especificar, Django també assumeix 'id'
     */
    protected $primaryKey = 'id';


    //CAMPOS DE LA BD
    //En Laravel, per seguretat, has d'especificar quins camps es poden omplir així:
    //$fillable = camps que SÍ es poden assignar massivament
    //$guarded = camps que NO es poden assignar (el contrari)
    //si nomes omples fillable s'entén que el que no està a fillable és guarded i viceversa
    protected $fillable = [ 'nombre', 'procedencia', 'fecha_cumpleanos', 'descripcion', 'estado', 'factory_id' ];

    //casts, util per a forçar un casteig d'una variable a enum o a date
    /**
     * CASTS - Conversió automàtica de tipus
     *
     * En Django els tipus es defineixen al model:
     * fecha_cumpleanos = models.DateField()
     * estado = models.CharField(choices=ESTADO_CHOICES)
     *
     * En Laravel els tipus es defineixen a la migració, però aquí podem FORÇAR conversions:
     * - 'fecha_cumpleanos' => 'date': converteix automàticament a Carbon (DateTime de Laravel)
     * - 'estado' => EstadoFemboy::class: converteix automàticament a l'enum
     *
     * Quan llegeixis $femboy->fecha_cumpleanos, serà un objecte Carbon
     * Quan llegeixis $femboy->estado, serà un EstadoFemboy (enum)
     */
    protected function casts(): array
    {
        return [
            'estado' => EstadoFemboy::class,
            'fecha_cumpleanos' => 'date',
        ];
    }

    protected $appends = ['edad'];


    public function edad(): Attribute
    {
        return Attribute::make(
            get: fn () => \Carbon\Carbon::parse($this->attributes['fecha_cumpleanos'])->age,
        );
    }

    //1-N HASMANY
    //N-1 BELONGSTO
    //N-N BELONGSTOMANY
    //1-1 HASONE
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }


    /**
     * RELACIÓ N-N: Femboy està relacionat amb MOLTS Customers
     **
     * En Laravel:
     * - Cal una taula intermèdia: 'customer_femboy'
     * - Laravel segueix la convenció: model1_model2 en ordre alfabètic
     * - Si la taula es diu diferent, ho especifiques
     *
     * Paràmetres:
     * 1r: Model relacionat (Customer)
     * 2n: Nom de la taula intermèdia (si no segueix la convenció)
     * 3r: Clau forana d'aquest model a la taula intermèdia
     * 4t: Clau forana de l'altre model a la taula intermèdia
     *
     * ->withTimestamps(): La taula intermèdia tindrà created_at i updated_at
     *
     * Ús:
     * $femboy->customers          → Collection de tots els customers
     * $femboy->customers()->attach($customer_id)    → Afegir relació
     * $femboy->customers()->detach($customer_id)    → Eliminar relació
     * $femboy->customers()->sync([$id1, $id2])      → Sincronitzar (elimina les velles, afegeix les noves)
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_femboy', 'femboy_id', 'customer_id')->withTimestamps();
    }



}
