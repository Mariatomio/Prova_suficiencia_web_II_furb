<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComandaProduto extends Model
{
    use HasFactory;
    protected $table = 'comanda_produtos';
    protected $hidden = ['idComanda', 'idProduto'];
    protected $fillable = [
        'idComanda',
        'idProduto'
    ];

    public function comandas() {
        return $this->belongsTo(Comanda::class, 'idComanda');
    }

    public function produtos() {
        return $this->belongsTo(Produto::class, 'idProduto');
    }
}
