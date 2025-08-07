<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;
    protected $table = 'produtos';

    protected $hidden = ['pivot'];

    protected $fillable = [
        'nome',
        'preco',
    ];

    public function comandas()
    {
        return $this->belongsToMany(Comanda::class, 'comanda_produtos', 'idProduto', 'idComanda');
    }
}
