<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comanda extends Model
{    
    use HasFactory;
    protected $table = 'comandas';
    protected $hidden = ['idUsuario'];

    protected $fillable = [
        'idUsuario',
    ];
    public function usuario() {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }
    
    public function produtos() {
        return $this->belongsToMany(Produto::class, 'comanda_produtos', 'idComanda', 'idProduto');
    }
}
