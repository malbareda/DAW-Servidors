<?php

namespace App\Models;

use Illuminate\Contracts\Support\HasOnceHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Factory extends Model
{
    use HasFactory;
    //Nombre de la table. Tiene que ser igual al nombre de la migracion.
    protected $table = 'factories';
    //LLAVE PRIMARIA. PODEU NO CREAR-LA PERO SI LA CREEU SIUSPLAU DIGUEU-LI id
    protected $primaryKey = 'id';
    protected $fillable = ['nombre','ubicacion','cif'];

    //1-N HASMANY
    //N-1 BELONGSTO
    //N-N BELONGSTOMANY
    //1-1 HASONE
    public function femboys(): HasMany
    {
        return $this->hasMany(Femboy::class);
    }

    public function customerPatrocinador(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

}
