<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clerk extends Model
{
    protected $fillable = ['first_name','last_name','sex'];
    public $timestamps = false;
    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
