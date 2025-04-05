<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orden extends Model
{
    use HasFactory;
    protected $fillable = ['fecha', 'usuario_id', 'mesa_id', 'estado', 'caja_id'];

    public function mesero()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function articulos()
    {
        return $this->hasMany(Articulo::class);
    }
}
