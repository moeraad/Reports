<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class userProfile extends Model
{
    public $timestamps = false;
    protected $table = 'user_profile';
    protected $fillable = ['user_id','provinces'];
}
