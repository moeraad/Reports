<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class courtFields extends Model
{
    function courtName()
    {
        $this->belongsTo("App\Name");
    }
}
