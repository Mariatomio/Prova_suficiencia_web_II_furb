<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $hidden = ['password'];
    protected $fillable = [
        'nomeUsuario',
        'telefoneUsuario',
        'email',
        'password',
    ];

    public function comandas() {
        return $this->hasMany(Comanda::class, 'idUsuario', 'id');
    }
}
