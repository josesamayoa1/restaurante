<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

       protected $table = 'roles';
        protected $fillable = ['name','guard_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

}
