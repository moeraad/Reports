<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Judgement extends Model
{
    protected $fillable = ['judge_court_id','report_date','arrival_date','last_session','judgement_date','judge_id','rule_number','speciality_id','status_id','judgment_type_id','sessions_count','notes','decision_source','created_by'];
    
    public function judgmentArticles()
    {
        return $this->hasOne("App\judgmentArticles");
    }
}
