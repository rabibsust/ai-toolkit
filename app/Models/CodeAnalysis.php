<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeAnalysis extends Model
{
    protected $fillable = [
        'code',
        'analysis',
        'suggestions',
        'score',
        'file_name',
        'provider',
        'cost',
        'tokens_used'
    ];

    protected $casts = [
        'suggestions' => 'array',
        'score' => 'integer',
        'cost' => 'decimal:6',
        'tokens_used' => 'integer'
    ];
}
