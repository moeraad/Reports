<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configs extends Model
{
    protected $fillable = ['province_id','district_id','zone_id','court_degree_id','role_id','count'];
    public $timestamps = false;
    //
}
