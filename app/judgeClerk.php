<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class judgeClerk extends Model
{
    protected $fillable = ['clerk_id','judge_id'];
    public $timestamps = false;
}
