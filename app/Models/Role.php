<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

       protected $table = 'roles';
        protected $fillable = ['name','guard_name', 'status'];

        public function users()
        {
            return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
        }

}
