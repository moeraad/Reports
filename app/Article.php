<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function judgmentArticles()
    {
        return $this->hasMany("App\judgmentArticles");
    }
}
