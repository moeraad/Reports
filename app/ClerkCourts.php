<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClerkCourts extends Model
{
    protected $fillable = ['court_id','clerk_id'];
    public $timestamps = false;
}
