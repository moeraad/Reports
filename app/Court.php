<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Court extends Model
{   
    protected $fillable = ['title','court_type_id','province_id','district_id','zone_id','court_degree_id','court_name_id','room'];
    public function courtName()
    {
        return $this->belongsTo("App\Name");
    }
    
    public function courtType()
    {
        return $this->belongsTo("App\Type");
    }
    
    public function judgeCourt()
    {
        return $this->hasOne("App\judgeCourt");
    }
}
