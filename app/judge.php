<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class judge extends Model
{
    protected $fillable = ['first_name','middle_name','last_name','placenaisssance','residence','dateenservice','dateenjuridiction','datepromotion','degree',
        'phone','mobile','birth_date','birth_place','email','car_number','province_id','district_id','zone_id','phone2','office','sect','sex','car_num',
        'car_type','mother_name','register_number','date_service','date_juridation','date_promotion','retired','active'];
    public $timestamps = false;
    
    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
