<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    protected $fillable = ['fecha', 'mesero_id', 'mesa_id', 'estado'];

    public function mesero()
    {
        return $this->belongsTo(User::class, 'mesero_id');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
}
