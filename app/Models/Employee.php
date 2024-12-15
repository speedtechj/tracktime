<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'users';
    protected $guarded = [];
    public function tracktimes(){
        return $this->hasMany(Tracktime::class, 'user_id');
    }
}
