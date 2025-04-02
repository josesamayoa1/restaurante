<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
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
        'role_id',
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
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cortes()
    {
        return $this->hasMany(Corte::class);
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'mesero_id');
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isCajero()
    {
        return $this->role_id === 2;
    }

    public function isMesero()
    {
        return $this->role_id === 3;
    }

    public function isCocinero()
    {
        return $this->role_id === 4;
    }

}
