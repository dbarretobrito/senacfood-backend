
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favoritos extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receita_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receita()
    {
        return $this->belongsTo(Receitas::class);
    }
}
