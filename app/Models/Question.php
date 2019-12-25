<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{   
    protected $fillable = [
        'question', 'choices'
    ];

    protected $casts = [
        'choices' => 'array', // Will converted to (Array)
    ];
}
