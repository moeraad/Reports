<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class judgmentArticles extends Model
{
    public function Articles()
    {
        return $this->belongsToMany("App\Articles");
    }
    
    public function Judgement()
    {
        return $this->belongsTo("App\Judgement");
    }
}
