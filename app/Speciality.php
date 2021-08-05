<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    public function monthlyReport()
    {
        return $this->hasMany("App\monthlyReport");
    }
}
