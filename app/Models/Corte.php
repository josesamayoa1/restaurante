<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Corte extends Model
{
    use HasFactory;
    protected $fillable = ['saldo_inicial', 'saldo_final', 'caja_id', 'usuario_id', 'fecha'];



    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

}
