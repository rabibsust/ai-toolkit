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
        'file_name'
    ];

    protected $casts = [
        'suggestions' => 'array',
        'score' => 'integer'
    ];
}
