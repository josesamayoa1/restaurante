<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoPago extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
