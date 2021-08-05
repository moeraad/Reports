<?php

namespace App\Http\Controllers;

use App\Clerk;
use App\Degree;
use App\District;
use App\Http\Controllers\Controller;
use App\judge;
use App\judgeCourt;
use App\judgmentType;
use App\Name;
use App\Province;
use App\reportsSeparated;
use App\Role;
use App\Separated;
use App\Speciality;
use App\Type;
use App\Zone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;

class ReportsController extends Controller
{
    function judgmentsAverage()
    {
        $judgments = DB::select(DB::raw(''
                                . 'SELECT judges.first_name,judges.last_name, judgements.judgment_type_id, judgements.speciality_id,'
                                . 'Count(judgements.id) AS judgements_count,judgements.arrival_date,judgements.judgement_date, '
                                . 'Sum(COALESCE(TIMESTAMPDIFF(MONTH,judgements.arrival_date,judgements.judgement_date),0)) '
                                . 'AS average FROM judgements INNER JOIN judges ON judgements.judge_id = judges.id '
                                . ' GROUP BY judges.id, judgements.judgment_type_id, judgements.speciality_id'));
        $types = judgmentType::lists('name','id');
        $specialities = Speciality::lists('title', 'id');
        
        return view('reports/judgment_average_report',  compact('judgments','types','specialities'));
    }
    
    function reportsStats()
    {
        $judge_courts = judgeCourt::with('Court','Judge')->get()->keyBy('id')->toArray();
        $types = Type::lists('type','id');
        $degrees = Degree::lists('degree','id');
        $specialities = Speciality::lists('title','id');
        $provinces = Province::lists('title','id');
        $districs = District::lists('title','id');
        $zones = Zone::lists('title','id');
        $separated = Separated::lists('title','id');
        
        $data = array();
        $count = 0;
        $time_range = GetRange();
        
        $courts_reports = judgeCourt::select('id')->with(array('monthlyReport' => function($query) use ($time_range){
        $query->selectRaw(DB::raw("speciality_id,judge_court_id,SUM(arriving) as arriving,SUM(totalSeparated) as totalSeparated,"
                . "SUBSTRING_INDEX( GROUP_CONCAT(CAST(monthly_reports.rotated AS CHAR) ORDER BY monthly_reports.year DESC,monthly_reports.month DESC), ',', 1 ) AS rotated,"
                . "SUBSTRING_INDEX( GROUP_CONCAT(CAST(monthly_reports.remainedCases AS CHAR) ORDER BY monthly_reports.year DESC,monthly_reports.month DESC), ',', 1 ) AS remained,"
                . "SUBSTRING_INDEX( GROUP_CONCAT(CAST(totalCases AS CHAR) ORDER BY year DESC,month DESC), ',', 1 ) AS totalCases,GROUP_CONCAT(id) AS ids"))
                ->where(function($query) use ($time_range)
                {
                    $query->where('month', '>', 8)
                    ->where('month', '<', 13)
                    ->where('year', '=', date('Y', strtotime($time_range['from'])))
                    ->orwhere(function($query) use ($time_range){
                        $query->where('month', '=', 1)
                        ->where('year', '=', date('Y', strtotime($time_range['to'])));
                    });
                })
                ->groupby('judge_court_id')->groupby('speciality_id');
        }))
        ->get()->keyBy('id')->toArray();
        
        
        foreach ($courts_reports as $court_reports)
        {
            foreach ($court_reports['monthly_report'] as $monthly_report)
            {
                $judge_court_id = $court_reports["id"];
                
                $reports = reportsSeparated::selectRaw('monthly_report_id,separated_id,sum(count) as count')
                        ->whereIn('monthly_report_id',  explode(',', $monthly_report["ids"]))
                        ->groupBy('separated_id')
                        ->get();
                
                $data[$count][] = $judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'];//name
                $data[$count][] = !empty($judge_courts[$judge_court_id]['court']['court_type_id'])?$types[$judge_courts[$judge_court_id]['court']['court_type_id']]:0;//type
                $data[$count][] = !empty($judge_courts[$judge_court_id]['court']['court_degree_id'])?$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']]:0;//degree
                $data[$count][] = @$specialities[$monthly_report['speciality_id']];
                $data[$count][] = $judge_courts[$judge_court_id]['court']['province_id'] > 0 ? $provinces[$judge_courts[$judge_court_id]['court']['province_id']]:'';//province
                $data[$count][] = $judge_courts[$judge_court_id]['court']['district_id'] > 0 ? $districs[$judge_courts[$judge_court_id]['court']['district_id']]:'';//district
                $data[$count][] = $judge_courts[$judge_court_id]['court']['zone_id'] > 0 ? $zones[$judge_courts[$judge_court_id]['court']['zone_id']]:'';//zone
                $data[$count][] = $monthly_report['arriving'];
                $data[$count][] = $monthly_report['rotated'];
                $data[$count][] = $monthly_report['remained'];
                $data[$count][] = $monthly_report['totalCases'];
                $data[$count][] = $monthly_report['totalSeparated'];
                
                $temp_separated = array();
                foreach ($reports as $report)
                {
                    $temp_separated[$report['separated_id']] = $report->count;
                }
                
                foreach($separated as $separated_id => $separated_item)
                {
                    if(isset($temp_separated[$separated_id]))
                    {
                        $data[$count][] = $temp_separated[$separated_id];
                    }
                    else 
                    {
                        $data[$count][] = 0;
                    }
                }
                $count++;
            }
        }
        
        return view('reports/detailed_report',  compact('separated','data'));
    }
    
    function reportsStatsData()
    {
        $input = Input::all();
        $judge_courts = judgeCourt::with('Court','Judge')->get()->keyBy('id')->toArray();
        $types = Type::lists('type','id');
        $degrees = Degree::lists('degree','id');
        $specialities = Speciality::lists('title','id');
        $provinces = Province::lists('title','id');
        $districs = District::lists('title','id');
        $zones = Zone::lists('title','id');
        $separated = Separated::lists('title','id');
        
        $data = array();
        $count = 0;
        
        $courts_reports = judgeCourt::select('id')->with(array('monthlyReport' => function($query){
                                $query->selectRaw(DB::raw("speciality_id,judge_court_id,SUM(totalSeparated) as totalSeparated,SUBSTRING_INDEX( GROUP_CONCAT(CAST(totalCases AS CHAR) ORDER BY year DESC,month DESC), ',', 1 ) AS totalCases,GROUP_CONCAT(id) AS ids"))->groupby('judge_court_id')->groupby('speciality_id');
                            }))->get()->keyBy('id')->toArray();
        
        foreach ($courts_reports as $court_reports)
        {
            foreach ($court_reports['monthly_report'] as $monthly_report)
            {
                $reports = reportsSeparated::selectRaw('monthly_report_id,separated_id,sum(count) as count')
                        ->whereIn('monthly_report_id',  explode(',', $monthly_report["ids"]))
                        ->groupBy('separated_id')
                        ->get();
                $judge_court_id = $court_reports["id"];
                
                $data[$count][] = $judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'];//name
                $data[$count][] = @$types[$judge_courts[$judge_court_id]['court']['court_type_id']];//type
                $data[$count][] = $degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']];//degree
                $data[$count][] = $specialities[$monthly_report['speciality_id']];
                $data[$count][] = $judge_courts[$judge_court_id]['court']['province_id'] > 0 ? $provinces[$judge_courts[$judge_court_id]['court']['province_id']]:'';//province
                $data[$count][] = $judge_courts[$judge_court_id]['court']['district_id'] > 0 ? $districs[$judge_courts[$judge_court_id]['court']['district_id']]:'';//district
                $data[$count][] = $judge_courts[$judge_court_id]['court']['zone_id'] > 0 ? $zones[$judge_courts[$judge_court_id]['court']['zone_id']]:'';//zone
                $data[$count][] = $monthly_report['totalCases'];
                $data[$count][] = $monthly_report['totalSeparated'];
                
                $temp_separated = array();
                foreach ($reports as $report)
                {
                    $temp_separated[$report['separated_id']] = $report->count;
                }
                
                foreach($separated as $separated_id => $separated_item)
                {
                    if(isset($temp_separated[$separated_id]))
                    {
                        $data[$count][] = $temp_separated[$separated_id];
                    }
                    else 
                    {
                        $data[$count][] = 0;
                    }
                }
                $count++;
            }
        }

        $collection = new Collection($data);
        
        return Datatables::of($collection)->make();
    }
    
    
    public function ReadClerks()
    {
        $f = fopen("D:/clerks.csv", "r");
        while ($r = fgets($f))
        {
            $arr = explode(" ", iconv("Windows-1256", "UTF-8", trim($r)));
            
            $data[count($arr)][] = array(
                "name" => iconv("Windows-1256", "UTF-8", trim($r))
            );
        }
        
        fclose($f);
        
        $ins = array();
        foreach ($data[5    ] as $row)
        {
            $arr = explode(" ", $row["name"]);
            $ins[] = array(
                "first_name" => $arr[0] . " " . $arr[1],
                "last_name" => $arr[2] . " " . $arr[3] . " " . $arr[4]
            );
        }
        Clerk::insert($ins);
    }
    
    function usersStats()
    {
//        $this->ReadClerks();
//        exit;
          
        $data_range = GetRange();
        
        $judgements = DB::select(DB::raw('select count(`judgements`.`id`) AS `count`,`users`.`name` AS `name`,`report_date` as date '
                . 'from (`judgements` left join `users` on((`users`.`id` = `judgements`.`created_by`))) '
                . 'where report_date BETWEEN "'.$data_range['from'].'" and "'.$data_range['to'].'" '
                . 'group by `judgements`.`created_by`,`report_date` order by `users`.`name`,`report_date` asc'));
        
        
        //2016-11-01
        $reports = DB::select(DB::raw('select count(`monthly_reports`.`id`) AS `count`,`users`.`name` AS `name`,concat(`year`,"-",IF(month>9,`month`,concat("0",`month`)),"-01") as date '
                . 'from (`monthly_reports` left join `users` on((`users`.`id` = `monthly_reports`.`created_by`))) '
                . 'where month BETWEEN "7" and "12" and year="'.date("Y",  strtotime($data_range["from"])).'" '
                . 'group by `monthly_reports`.`created_by`,`month`,`year` order by `users`.`name`,`year`,`month`'));
        
        $num_courts = DB::select(DB::raw('SELECT COUNT(DISTINCT(judge_court_id)) as count,`users`.`name` AS `name`,concat(`year`,"-",IF(month>9,`month`,concat("0",`month`)),"-01") as date '
                . 'FROM (`monthly_reports` left join `users` on((`users`.`id` = `monthly_reports`.`created_by`))) '
                . 'where ((month BETWEEN "9" and "12" and year="'.date("Y",  strtotime($data_range["from"])).'") '
                . 'or (month BETWEEN "1" and "8" and year="'.date("Y",  strtotime($data_range["to"])).'") ) '
                . 'GROUP BY created_by,month,year'));
        
        $num_judments = DB::select(DB::raw('SELECT `users`.`name`,`report_date` as `date`,COUNT(distinct `judge_court_id`) as count '
                . 'FROM `judgements` left join `users` on `users`.`id` = `judgements`.`created_by` '
                . 'where report_date BETWEEN "'.$data_range['from'].'" and "'.$data_range['to'].'" '
                . 'GROUP BY `report_date`,`created_by` '
                . 'order by `created_by`,`judge_court_id`,`report_date`'));
        
        $stats = [];
        
        foreach ($judgements as $judgement)
        {
            $stats[$judgement->name][$judgement->date]["judgement"] = $judgement->count;
        }
        
        foreach ($reports as $report)
        {
            $stats[$report->name][$report->date]["report"] = $report->count;
        }
        
        foreach ($num_courts as $num_court)
        {
            $stats[$num_court->name][$num_court->date]["count"] = $num_court->count;
        }
        
        foreach ($num_judments as $num_judment)
        {
            $stats[$num_judment->name][$num_judment->date]["nb_judgement_tbls"] = $num_judment->count;
        }
        
        return view('reports/users_stats',compact('stats'));
    }
    
    function judgesDistribution()
    {
        $judges_dist = DB::select(DB::raw('SELECT COUNT(judges.id) as judges_count,
                                    provinces.title as province,
                                    roles.title as role

                                    FROM (SELECT `judge_id`,`province_id`,`role_id`
                                    FROM `judge_courts` 
                                    inner join `courts` 
                                    on `courts`.`id`= `judge_courts`.`court_id`
                                    GROUP BY `judge_id`,`province_id`,`role_id`
                                    
                                    UNION ALL
                                    
                                    SELECT `advisors`.`judge_id`,`courts`.`province_id`,4 as role_id
                                    FROM `advisors`
                                    inner join `judge_courts` on `judge_courts`.`id`= `advisors`.`judge_court_id`
                                    inner join `courts` on `judge_courts`.`court_id`=`courts`.`id`
                                    GROUP BY `judge_id`,`province_id`,`role_id`
                                    ) as s

                                    inner join roles on roles.id=s.role_id
                                    inner join provinces on provinces.id=s.province_id
                                    inner join judges on judges.id=s.judge_id
                                    
                                    GROUP BY province,role'));
        
        return view('reports/judges_dist',compact('judges_dist'));
    }
    
    function userLogs()
    {
        $monthly_reports = DB::select(DB::raw("SELECT monthly_reports.judge_court_id, CONCAT(`year`,'-',IF(month>9,`month`,concat('0',`month`)),'-01') as monthly_date,(select count(judgements.id) from judgements where judgements.judge_court_id=monthly_reports.judge_court_id and judgements.report_date=CONCAT(`year`,'-',IF(month>9,`month`,concat('0',`month`)),'-01')) as judgements,monthly_reports.created_at
                                    FROM `monthly_reports` 
                                    where monthly_reports.created_by=".Auth::user()->id."
                                    group by monthly_reports.judge_court_id,monthly_date
                                    order by monthly_reports.created_at desc"));
        
        $courts = judgeCourt::select(DB::raw('judge_courts.id, courts.title,CONCAT(judges.first_name," ",judges.middle_name, " ", judges.last_name) as judge'))
                ->join("courts","courts.id","=","judge_courts.court_id")
                ->join("judges","judges.id","=","judge_courts.judge_id")
                ->get()
                ->keyBy("id");
        
        return view('reports/user_logs',compact('monthly_reports','judgements','courts'));
    }
    
    function fullReport()
    {
        $provinces = Province::orderBy('title')->lists('title','id');
        $districs = District::orderBy('title')->lists('title','id');
        $names = Name::lists('name','id');
        $types = Type::lists('type','id');
        $degrees = Degree::lists('degree','id');
        $specialities = Speciality::lists('title','id');
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        
        return view('reports/full_report',  compact('data','fields','names','types','degrees','provinces','districs','zones','specialities','judges'));
    }
    
    function fullAjaxReport()
    {
        $input = Input::all();
        $courts_reports = $this->buildReportQuery($input);
        $getdata = $this->buildReportDataArray($courts_reports);
        
        $data = $getdata["data"];
        $fields = $getdata["fields"];
        
        echo view('reports/full_report_ajax',  compact('data','fields'));
    }
    
    
    function judgesByOccupation()
    {
        $roles = Role::lists('title','id');
        $types = Type::lists('type','id');
        $province = Province::lists('title','id');
        $degree = Degree::lists('degree','id');
        
        $judges = judgeCourt::select("first_name","middle_name","last_name","role_id","courts.court_type_id","courts.court_degree_id","sect","courts.province_id")
                ->join("judges","judges.id","=","judge_courts.judge_id")
                ->join("courts","courts.id","=","judge_courts.court_id")
                ->groupBy("first_name","middle_name","last_name","role_id")
                ->orderBy("role_id")
                ->get();
        
        return view('reports/judges_by_occupation',  compact('judges','roles','types','province','degree'));
    }
    
    public function buildReportDataArray($courts_reports)
    {
        $judge_courts = judgeCourt::with('Court','Judge')->get()->keyBy('id')->toArray();
        $types = Type::lists('type','id');
        $degrees = Degree::lists('degree','id');
        $specialities = Speciality::lists('title','id');
        $provinces = Province::lists('title','id');
        $districs = District::lists('title','id');
        $separated = Separated::lists('title','id'); 
        
        $data = array();
        $count = 0;
        
        foreach ($courts_reports as $monthly_report)
        {
            $judge_court_id = $monthly_report["judge_court_id"];

            $reports = reportsSeparated::selectRaw('separated_id,sum(count) as count')
                    ->whereIn('monthly_report_id',  explode(',', $monthly_report["ids"]))
                    ->groupBy('separated_id')
                    ->get()->toArray();

            $zone = @$judge_courts[$judge_court_id]['court']['zone_id'] ? @$judge_courts[$judge_court_id]['court']['zone_id']: 
                    @$districs[$judge_courts[$judge_court_id]['court']['district_id']] ? @$districs[$judge_courts[$judge_court_id]['court']['district_id']] :
                    @$provinces[$judge_courts[$judge_court_id]['court']['province_id']];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["total_separated"] = $monthly_report['totalSeparated'];
            
            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["rotated"] = $monthly_report['rotated'];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["total_cases"] = $monthly_report['totalCases'];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["arriving"] = $monthly_report['arriving'];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["arrivalDirectComplaint"] = $monthly_report['arrivalDirectComplaint'];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["pretencesArrival"] = $monthly_report['pretencesArrival'];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["totalSeparated"] = $monthly_report['totalSeparated'];

            $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["remainedCases"] = $monthly_report['remainedCases'];

            foreach ($reports as $report)
            {
                $data[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    [ @$zone ]
                    [ @$judge_courts[$judge_court_id]['judge']['first_name'] . " " . $judge_courts[$judge_court_id]['judge']['last_name'] ]
                    [ @$specialities[$monthly_report['speciality_id']] ]["separated"][ @$separated[$report['separated_id']] ] = $report["count"];

                $fields[ @$provinces[$judge_courts[$judge_court_id]['court']['province_id']] ]
                    [ @$degrees[$judge_courts[$judge_court_id]['court']['court_degree_id']] ]
                    [ @$types[$judge_courts[$judge_court_id]['court']['court_type_id']] ]
                    ["separated_fields"][ @$separated[$report['separated_id']] ] = @$separated[$report['separated_id']];
            }
        }
        return ["data" => $data,"fields" => $fields];
    }
    
    public function buildReportQuery($input)
    {
        $time_range = GetRange();
        
        return judgeCourt::selectRaw(DB::raw(""
                . "monthly_reports.speciality_id,"
                . "monthly_reports.judge_court_id,"
                . "SUM(monthly_reports.totalSeparated) as totalSeparated,"
                . "SUM(monthly_reports.arriving) as arriving,"
                . "SUM(monthly_reports.arrivalDirectComplaint) as arrivalDirectComplaint,"
                . "SUM(monthly_reports.pretencesArrival) as pretencesArrival,"
                . "SUBSTRING_INDEX( GROUP_CONCAT(CAST(monthly_reports.totalCases AS CHAR) ORDER BY monthly_reports.year DESC,monthly_reports.month DESC), ',', 1 ) AS totalCases,"
                . "SUBSTRING_INDEX( GROUP_CONCAT(CAST(monthly_reports.rotated AS CHAR) ORDER BY monthly_reports.year DESC,monthly_reports.month DESC), ',', 1 ) AS rotated,"
                . "SUBSTRING_INDEX( GROUP_CONCAT(CAST(monthly_reports.remainedCases AS CHAR) ORDER BY monthly_reports.year DESC,monthly_reports.month DESC), ',', 1 ) AS remainedCases,"
                . "SUM(monthly_reports.totalSeparated) as totalSeparated,"
                . "GROUP_CONCAT(monthly_reports.id) AS ids"))
                ->join( "courts","courts.id","=","judge_courts.court_id" )
                ->where(function($query) use ($time_range)
                {
                    $query->where('month', '>', 8)
                    ->where('month', '<', 13)
                    ->where('year', '=', date('Y', strtotime($time_range['from'])))
                    ->orwhere(function($query) use ($time_range)
                    {
                        $query->where('month', '=', 1)
                        ->where('year', '=', date('Y', strtotime($time_range['to'])));
                    });
                })->where(function($query) use ($input)
                {
                    if(!empty($input["filters"]["province"]))
                        $query->whereIn( "courts.province_id", $input["filters"]["province"] );
                    if(!empty($input["filters"]["district"]))
                        $query->whereIn( "courts.district_id", $input["filters"]["district"] );
                    if(!empty($input["filters"]["zone"]))
                        $query->whereIn( "courts.zone_id", $input["filters"]["zone"] );
                    if(!empty($input["filters"]["name"]))
                        $query->whereIn( "courts.court_name_id",  $input["filters"]["name"] );
                    if(!empty($input["filters"]["type"]))
                        $query->whereIn( "courts.court_type_id", $input["filters"]["type"] );
                    if(!empty($input["filters"]["degree"]))
                        $query->whereIn( "courts.court_degree_id", $input["filters"]["degree"] );
                    if(!empty($input["filters"]["judge"]))
                        $query->whereIn( "judge_courts.judge_id", $input["filters"]["judge"] );
                })
                ->join('monthly_reports',"monthly_reports.judge_court_id","=","judge_courts.id")
                ->where(function($query) use ($input)
                {
                    if(!empty($input["filters"]["speciality"]))
                        $query->whereIn( "speciality_id", $input["filters"]["speciality"] );
                })->groupby('monthly_reports.judge_court_id')
                ->groupby('monthly_reports.speciality_id')->get()->toArray();
    }
}
