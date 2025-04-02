<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    use HasFactory;
    protected $fillable = ['nombre'];


    public function cortes()
    {
        return $this->hasMany(Corte::class);
    }
}
