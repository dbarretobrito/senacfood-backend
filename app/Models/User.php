<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'perfil',
        'password',
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
     public function receitas()
    {
        return $this->hasMany(Receitas::class);
    }

    public function ingredientes()
    {
        return $this->hasMany(Ingredientes::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categorias::class);
    }

    public function favoritos()
    {
        return $this->belongsToMany(
        \App\Models\Receitas::class, 
        'favoritos',   
        'user_id', 
        'receita_id' 
    )->withTimestamps();
    }
}
