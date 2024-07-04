<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyExit extends Model
{
    use HasFactory;

    protected $table = 'exits';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_exit',
        'description',
        'value',
        'id_user_fk',
        'exit_date'
    ];

    protected $primaryKey = 'id_exit';
}
