<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class monthlyReport extends Model
{
    protected $fillable = ['judge_court_id','speciality_id','judge_id','arriving','eliminatedArrival','rotated','casesOnSchedule','primaryReport','protectionMeasures','totalSeparated','totalCases','remainedCases','pretencesArrival','arrivalDirectComplaint','forExecution','executed','date','year','month'];
    
    public function judgeCourt()
    {
        return $this->belongsTo('App\judgeCourt');
    }
    
    public function reportsSeparated()
    {
        return $this->hasMany('App\reportsSeparated');
    }
    
    public function getReportShortDateAttribute() {
        return date('Y-m-d',  strtotime($this->year . '-' . $this->month . '-' . '01'));
    }
}
