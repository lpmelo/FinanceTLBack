<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyEntrie extends Model
{
    use HasFactory;

    protected $table = 'entries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_entrie',
        'description',
        'value',
        'id_user_fk',
        'entrie_date'
    ];

    protected $primaryKey = 'id_entrie';
}
