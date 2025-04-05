<?php

namespace App\Models;
use Spatie\Permission\Traits\HasRoles;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'edad',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->nombre)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }


    public function cortes()
    {
        return $this->hasMany(Corte::class);
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'user_id');
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isCajero()
    {
        return $this->role->name === 'cajero';
    }

    public function isMesero()
    {
        return $this->role->name === 'mesero';
    }

    public function isCocinero()
    {
        return $this->role->name === 'cocinero';
    }

}
