<?php

function years()
{
    $list = ['2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'];
    return array_combine($list, $list);
}

function months()
{
    $list = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
    return array_combine($list, $list);
}

function monthName($m)
{
    $list = [
        '',
        'كانون الثاني',
        'شباط',
        'آذار',
        'نيسان',
        'أيار',
        'حزيران',
        'تموز',
        'آب',
        'أيلول',
        'تشرين أول',
        'تشرين الثاني',
        'كانون الأول'
    ];
    return $list[$m];
}

function getMonthByIndex($index)
{
    $list = ['9', '10', '11', '12', '1', '2', '3', '4', '5', '6', '7', '8'];
    return isset($list[$index])?$list[$index]:0;
}

function checkIsAValidDate($myDateString){
    return (bool)strtotime($myDateString);
}

function getYearByMonthIndex($index)
{
    $selected_month = getMonthByIndex($index);
    $current_year = Session::get('current_year');
    $month =  date('m');
    $year =  date('Y');
    
    if( ($month >= 9 && $month <= 12) && ($selected_month >= 1 && $selected_month <= 8) )
            $current_year+=1;
    
    if( ($month >= 1 && $month <= 8) && ($selected_month >= 9 && $selected_month <= 12) )
            $current_year-=1;
    
    return $current_year;
}

function previousMonth($date)
{
    return date('Y-m-d', strtotime(date('Y-m', strtotime($date)) . " -1 month"));
}

function GetSects()
{
    return [
        "شيعي" => "شيعي",
        "سني" => "سني",
        "ماروني" => "ماروني",
        "روم كاثوليك" => "روم كاثوليك",
        "روم أرثوذكس" => "روم أرثوذكس",
        "أرمن كاثوليك" => "أرمن كاثوليك",
        "أرمن أرثوذكس" => "أرمن أرثوذكس",
        "سريان كاثوليك" => "سريان كاثوليك",
        "لاتيني" => "لاتيني",
        "درزي" => "درزي"
    ];
}

function GetRange()
{
    $month = date('m');
    $year =  date('Y');
    $range = [];
    $current_year = Session::get('current_year');
    
    if( $month >= 9 && $month <= 12 )
    {
        $range['from'] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime(date($current_year . '-9-1 00:00:00'))));
        $range['to'] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime(date($current_year . '-8-30 00:00:00') . " +1 year")));
    }
    
    if( $month >= 1 && $month <= 8 )
    {
        $range['from'] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime(date($current_year . '-9-1 00:00:00') . " -1 year")));
        $range['to'] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime(date($current_year . '-8-30 00:00:00'))));
        
    }
    
    return $range;
    //return ["from" => "2016-9-1 00:00:00","to" => "2017-8-30 00:00:00"];
}

function GetFields($filtered_fields = [])
{
    $temp = [];
    $fields = [
        "father_name" => "إسم الأب",
        "mother_name" => "إسم الأم",
        "sect" => "الطائفة",
        "sex" => "الجنس",
        "birth_date" => "تاريخ الولادة",
        "birth_place" => "مكان الولادة",
        "phone" => "هاتف",
        "phone2" => "منزل",
        "office" => "مكتب",
        "mobile" => "جوال",
        "email" => "بريد",
        "car_number" => "رقم السيارة",
        "residence" => "مكان السكن",
        "province_id" => "محافظة",
        "district_id" => "قضاء",
        "zone_id" => "منطقة",
        "date_service" => "تاريخ الخدمة",
        "date_juridation" => "تاريخ القضاء",
        "date_promotion" => "تاريخ الترقية",
        "degree" => "الدرجة"
    ];
    
    if(!empty($filtered_fields))
    {
        foreach($filtered_fields as $field)
        {
            $temp[$field] = $fields[$field];
        }
        return $temp;
    }
    else
    {
        return $fields;
    }
}
