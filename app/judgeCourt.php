<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class judgeCourt extends Model
{
    protected $fillable = ['court_id','judge_id','role_id','date_from','date_to'];
    
    public function Court()
    {
        return $this->belongsTo("App\Court");
    }
    
    public function Judge()
    {
        return $this->belongsTo("App\judge");
    }
    
    public function monthlyReport()
    {
        return $this->hasMany(monthlyReport::class);
    }
}
