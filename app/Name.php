<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    public function Court()
    {
        return $this->hasMany("App\Court");
    }
    
    public function Fields()
    {
        return $this->hasMany("App\courtFields","court_name_id")->orderby('order');
    }
}
