<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticker',
        'date',
        'open',
        'high',
        'low',
        'close',
        'volume'
    ];

    protected $casts = [
        'date' => 'date',
    ];
}