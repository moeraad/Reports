<?php

namespace App\Http\Controllers;

use App\Degree;
use App\District;
use App\Http\Controllers\Controller;
use App\Http\Requests\judgesRequest;
use App\judge;
use App\judgeCourt;
use App\judgmentType;
use App\Province;
use App\reportsSeparated;
use App\Separated;
use App\Speciality;
use App\Type;
use App\Zone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

class ManagejudgesController extends Controller
{
    public function index()
    {
        $judges = judge::where('retired',0)->where('active',1)->get();
        $provinces = Province::lists('title','id');
        $districts = District::lists('title','id');
        $zones = Zone::lists('title','id');
        $page_title = "عرض القضاة";
        $sects = GetSects();
        $fields = GetFields(["sect","sex","birth_date","birth_place","residence","mobile","date_juridation","date_promotion","degree"]);
        $all_fields = GetFields();
        
        return view('manage_judges/list_judges', compact("judges","provinces","districts","zones","page_title","sects","fields","all_fields"));
    }

    public function filter()
    {
        $inputs = Input::all();
        Input::flash();
        $fields = GetFields($inputs['filter_fields']);
        $all_fields = GetFields();
        //DB::enableQueryLog();
        $judges = judge::where(function($q) use ($inputs,$fields){
            $province_id = $inputs['province_id'];
            $district_id = $inputs['district_id'];
            $zone_id = $inputs['zone_id'];
            $sect = $inputs['sect'];
            $degree = $inputs['degree'];
            $sex = isset($inputs['sex'])?$inputs['sex']:'';
            $retired = isset($inputs['retired'])?$inputs['retired']:0;
            $active = isset($inputs['active'])?$inputs['active']:0;
            
            
            if( $degree != ''){
                $degree_start = explode(',',$degree)[0];
                $degree_end = explode(',',$degree)[1];
            }
            $date_service = $inputs['date_service'];
            if( $date_service != ''){
                $date_service_start = explode(' - ',$date_service)[0];
                $date_service_end = explode(' - ',$date_service)[1];
            }
            $date_juridation = $inputs['date_juridation'];
            if( $date_juridation != ''){
                $date_juridation_start = explode(' - ',$date_juridation)[0];
                $date_juridation_end = explode(' - ',$date_juridation)[1];
            }
            $date_promotion = $inputs['date_promotion'];
            if( $date_promotion != ''){
                $date_promotion_start = explode(' - ',$date_promotion)[0];
                $date_promotion_end = explode(' - ',$date_promotion)[1];
            }
            
            if($province_id > 0)
                $q->where('province_id',$province_id);
            if($district_id > 0)
                $q->where('district_id',$district_id);
            if($zone_id > 0)
                $q->where('zone_id',$zone_id);
            if($sect != '')
                $q->where('sect',$sect);
            if($retired != '')
                $q->where('retired',$retired);
            if($active != '')
                $q->where('active',$active);
            if($sex != '')
                $q->where('sex',$sex);
            if($date_service != '')
                $q->whereBetween('date_service',[$date_service_start,$date_service_end]);
            if($date_juridation != '')
                $q->whereBetween('date_juridation',[$date_juridation_start,$date_juridation_end]);
            if($date_promotion != '')
                $q->whereBetween('date_promotion',[$date_promotion_start,$date_promotion_end]);
            if($degree != '')
                $q->whereBetween('degree',[$degree_start,$degree_end]);
        })->get();
        
        //dd(DB::getQueryLog());
        $provinces = Province::lists('title','id');
        $districts = District::lists('title','id');
        $zones = Zone::lists('title','id');
        $page_title = "عرض القضاة";
        $sects = GetSects();
        
        return view('manage_judges/list_judges', compact("judges","provinces","districts","zones","page_title","sects","inputs","fields","all_fields"));
    }
    
    public function profile($id)
    {
        $judge = judge::find($id);
        $myCourts = judgeCourt::with('Court')->where('judge_id', $id)->get();

        $judgments = DB::select(DB::raw(''
                                . 'SELECT judges.first_name,judges.last_name, IF((judgements.judgment_type_id)>0, judgements.judgment_type_id, judgements.speciality_id) as judgment_type, '
                                . 'Count(judgements.id) AS judgements_count,judgements.arrival_date,judgements.judgement_date, '
                                . 'Sum(COALESCE(TIMESTAMPDIFF(MONTH,judgements.arrival_date,judgements.judgement_date),0)) '
                                . 'AS average FROM judgements INNER JOIN judges ON judgements.judge_id = judges.id '
                                . 'WHERE judges.id=' . $id
                                . ' GROUP BY judges.id, judgements.judgment_type_id, judgements.speciality_id'));
        
        $judgment_types = judgmentType::lists('name', 'id');
        $judge_courts = judgeCourt::with('Court', 'Judge')->get()->keyBy('id')->toArray();
        $data = $this->getJudgeDetailedWork($id);
        
        $types = Type::lists('type', 'id');
        $degrees = Degree::lists('degree', 'id');
        $specialities = Speciality::lists('title', 'id');
        $provinces = Province::lists('title', 'id');
        $districs = District::lists('title', 'id');
        $zones = Zone::lists('title', 'id');
        $separated = Separated::lists('title', 'id');
        $photo = File::exists($judge->photo)?url($judge->photo):url('photo/default-user.png');
        $sects = GetSects();
        
        return view('manage_judges/profile', compact('photo','judge', 'judge_courts', 'judgments', 'judgment_types', 'types', 'degrees', 'specialities', 'provinces', 'districs', 'zones', 'separated', 'data', 'myCourts','sects'));
    }

    public function getJudgeDetailedWork($id)
    {
        $data = [];
        $courts_reports = judgeCourt::select('id')->where('judge_id', $id)->with(array('monthlyReport' => function($query){
                                $query->selectRaw(DB::raw("speciality_id,judge_court_id,SUM(totalSeparated) as totalSeparated,SUBSTRING_INDEX( GROUP_CONCAT(CAST(totalCases AS CHAR) ORDER BY year DESC,month DESC), ',', 1 ) AS totalCases,GROUP_CONCAT(id) AS ids"))->groupBy('speciality_id');
                            }))->get()->keyBy('id')->toArray();

        foreach ($courts_reports as $court_reports)
        {
            foreach ($court_reports['monthly_report'] as $monthly_report)
            {
                $reports = reportsSeparated::selectRaw('monthly_report_id,separated_id,sum(count) as count')
                        ->whereIn('monthly_report_id', explode(',', $monthly_report["ids"]))
                        ->groupBy('separated_id')
                        ->get();

                $data[$court_reports["id"]][$monthly_report['speciality_id']]['total_separated'] = $monthly_report['totalSeparated'];
                $data[$court_reports["id"]][$monthly_report['speciality_id']]['total_cases'] = $monthly_report['totalCases'];
                foreach ($reports as $report)
                {
                    $data[$court_reports["id"]][$monthly_report['speciality_id']]['separated'][$report['separated_id']] = $report->count;
                }
            }
        }

        return $data;
    }

    public function create()
    {
        $provinces = Province::lists('title','id');
        $districts = District::lists('title','id');
        $zones = Zone::lists('title','id');
        $page_title = "إضافة قاضي";
        $sects = GetSects();
        
        return view('manage_judges/manage_judges', compact('provinces','districts','zones','page_title','sects'));
    }

    public function show($id)
    {
        $judge = judge::where("id", $id)->first();
        $provinces = Province::lists('title','id');
        $districts = District::lists('title','id');
        $zones = Zone::lists('title','id');
        $page_title = "القضاة";
        $sects = GetSects();
        
        return view('manage_judges/manage_judges', compact('judge','page_title','provinces','districts','zones','sects'));
    }

    public function destroy($id)
    {
        judge::find($id)->delete();
        return redirect("manage_judges");
    }

    public function store(judgesRequest $request)
    {
        $judge = new judge($request->all());
        $judge->save();

        return redirect("manage_judges/" . $judge->id);
    }

    public function update(judgesRequest $request)
    {
        $file = $this->upload($request->get("id"));
        
        $judge_id = $request->get("id");
        $judge = judge::findOrNew($judge_id);
        $judge->fill($request->all());
        
        if(empty($file) && $request->get('remove_photo') == 1)
            $judge->photo = '';
        if(!empty($file))
            $judge->photo = $file;
        
        $judge->save();
        
        return redirect("manage_judges/" . $judge->id);
    }

    public function upload($id)
    {
        if(null == Input::file('photo'))
            return '';
        
        $destinationPath = 'photo';
        $extension = Input::file('photo')->getClientOriginalExtension();
        $fileName = $id . '.' . $extension;
        Input::file('photo')->move($destinationPath, $fileName);
        
        return $destinationPath ."/". $fileName;
    }
}
