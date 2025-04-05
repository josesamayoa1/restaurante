<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;
    protected $fillable = ['factura_id', 'tipo_pago_id', 'cambio', 'monto'];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class);
    }
}
