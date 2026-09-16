<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory;
    //
    protected $table = 'customers';
    //LLAVE PRIMARIA. PODEU NO CREAR-LA PERO SI LA CREEU SIUSPLAU DIGUEU-LI id
    protected $primaryKey = 'id';
    protected $fillable = ['nom','dni','factory_id','activo','user_id'];
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class,'factory_id');
    }

    public function femboys(): BelongsToMany
    {
        return $this->belongsToMany(Femboy::class)->withTimestamps();
    }

    /**
     * Relació 1-1 amb User
     * Un Customer pertany a un User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
