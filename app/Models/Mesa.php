<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mesa extends Model
{
    use HasFactory;
    protected $fillable = ['nombre'];

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }
}
