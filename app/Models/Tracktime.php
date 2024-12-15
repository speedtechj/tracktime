<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracktime extends Model
{
    protected $guarded = [];
    protected $casts = [
        'imagecapture' => 'array',
    ];
}
