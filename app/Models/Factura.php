<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;
    protected $fillable = ['nit', 'orden_id', 'total', 'iva', 'fecha', 'estado'];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
