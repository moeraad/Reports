<?php

namespace App\Http\Controllers;

use App\Court;
use App\courtFields;
use App\Http\Controllers\Controller;
use App\Http\Requests\monthlyReportsBulkRequest;
use App\Http\Requests\monthlyReportsRequest;
use App\judge;
use App\judgeCourt;
use App\Judgement;
use App\monthlyReport;
use App\Name;
use App\reportsSeparated;
use App\Separated;
use App\Speciality;
use App\Type;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Html\FormFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class monthlyReportsController extends Controller
{
    public function index()
    {
        $page_title = 'عرض الجداول الشهرية';
        return view('manage_monthly_report/list_monthly_reports', compact('page_title'));
    }
    
    public function create()
    {
        $old = Input::old();
        $page_title = "الجداول الشهرية";
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $judge_courts = judgeCourt::all();
        $courts = Court::lists("title","id");
        $specialities = Speciality::Lists("title", "id");
        $field_to_hide = [];
        $fields = [];
        $separated = [];
        
        $separated = Separated::lists("title","id");
        
        if( isset($old['judge_court_id']) && !empty($old['judge_court_id']) )
        {
            $judge_court = judgeCourt::with("Court")->where('id',$old['judge_court_id'])->first();
            $court_name = Name::where('id',$judge_court->Court->court_name_id)->first();
            $court_type = Type::where('id',$judge_court->Court->court_type_id)->first();
            $field_to_hide = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
            $fields = judgeCourt::find($old['judge_court_id'])->Court->courtName->Fields;
            $separated = Separated::lists("title","id");
        }
        
        return view('manage_monthly_report/manage_monthly_report', compact('page_title','judges','judge_courts','courts','specialities','field_to_hide','fields','separated'));
    }
    
    public function bulkDelete($judge_court_id,$month,$year)
    {
        $saved_reports = monthlyReport::where(['judge_court_id'=>$judge_court_id,'month'=>$month,'year'=>$year])->lists('id','id');
        $saved_separated = reportsSeparated::whereIn('monthly_report_id', $saved_reports)->delete();
        monthlyReport::where(['judge_court_id'=>$judge_court_id,'month'=>$month,'year'=>$year])->delete();
        
        return redirect('monthly_reports/'. $judge_court_id .'/'. $month .'/'. $year);
    }
    
        
    public function bulkCreate()
    {
        $defaults       = ['judge_court_id'=>Session::get('judge_court_id'),'month'=>Session::get('month'),'year'=>Session::get('year')];
        $old            = Input::old();
        $last_report = monthlyReport::where("created_by", Auth::user()->id)->orderBy("created_at","desc")->limit(1)->get();
        
        $page_title     = "الجداول الشهرية";
        $judges         = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $retired_judges = judge::where("retired","1")->get()->lists("id", "id");
        
        $judge_courts   = judgeCourt::select("judge_courts.id","judge_courts.court_id","judge_courts.judge_id")
                ->join('courts', 'courts.id', '=', 'court_id')
                ->whereIn('Courts.province_id', explode(",", Session::get('provinces')))
                ->get();
        $courts         = Court::lists("title","id");
        $specialities   = Speciality::Lists("title", "id");
        $field_to_hide  = [];
        $fields         = [];
        $separated      = Separated::lists("title","id");

        if( isset($old['judge_court_id']) && !empty($old['judge_court_id']) )
        {
            $judge_court    = judgeCourt::with("Court")->where('id',$old['judge_court_id'])->first();
            $court_name     = Name::where('id',$judge_court->Court->court_name_id)->first();
            $court_type     = Type::where('id',$judge_court->Court->court_type_id)->first();
            $field_to_hide  = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
            $fields         = courtFields::where("court_name_id",$judge_court->Court->court_name_id)->where("court_type_id",$judge_court->Court->court_type_id)->orderBy("order")->get();
        }
            
        return view('manage_monthly_report/manage_monthly_report_bulk', compact('page_title','judges','judge_courts','courts','specialities','field_to_hide','fields','separated','defaults','last_report','retired_judges'));
    }
    
    public function showBulk($judge_court_id,$month,$year)
    {
        $defaults = [];
        $old = Input::old();
        
        $page_title = 'الجداول الشهرية';
        $monthly_report = monthlyReport::where(["judge_court_id"=>$judge_court_id,"month"=>$month,"year"=>$year])->orderBy("created_at")->get();
        $last_report = monthlyReport::where("created_by", Auth::user()->id)->orderBy("created_at","desc")->limit(1)->get();
        
        $count = $monthly_report->count();
        
        if($count == 0)
            return redirect("monthly_reports/bulk_create")->with(['judge_court_id'=>$judge_court_id,'month'=>$month,'year'=>$year]);
        
        $fields            = array();
        $separated_reports = array();
        $field_to_hide     = array();
        
        $temp_court_name = "";
        $temp_court_type = "";
        foreach ( $monthly_report as $report )
        {
            $separated_reports[] = reportsSeparated::where('monthly_report_id', '=', $report->id)->lists("count","separated_id");
        }
        
        {
            $judge_court    = judgeCourt::with("Court")->where('id',$judge_court_id)->first();
            
            $court_name_id = $judge_court->Court->court_name_id;
            $court_type_id = $judge_court->Court->court_type_id;
            $fields = courtFields::where("court_name_id",$judge_court->Court->court_name_id)->where("court_type_id",$judge_court->Court->court_type_id)->orderBy("order")->get();
            $court_name     = Name::where('id',$court_name_id)->first();
            $court_type     = Type::where('id',$court_type_id)->first();
            $temp_court_name = !empty($court_name)?$court_name->name : '';
            $temp_court_type = !empty($court_type)?$court_type->type : '';
            $field_to_hide     = $this->FieldsToHide($temp_court_name,$temp_court_type);
        }
        
        $separated = Separated::lists("title","id");
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $retired_judges = judge::where("retired","1")->get()->lists("id", "id");
        
        $judge_courts   = judgeCourt::select("judge_courts.id","judge_courts.court_id","judge_courts.judge_id")
                ->join('courts', 'courts.id', '=', 'court_id')
                ->whereIn('Courts.province_id', explode(",", Session::get('provinces')))
                ->get();
        $courts = Court::lists("title","id");
        $specialities = Speciality::Lists("title", "id");
        
        return view('manage_monthly_report/manage_monthly_report_bulk', compact('page_title','monthly_report','fields','separated','judges','judge_courts','judge_courts','judge_courts','courts','specialities','separated_reports','field_to_hide','count','judge_court_id','month','year','defaults','court_name_id','court_type_id',"last_report","retired_judges"));
    }
    
        
    public function store(monthlyReportsRequest $request)
    {
        $validator = Validator::make($request->all(),[]);
        
        //validate separated sums
        $validator->after(function($validator) use ($request) {
            $separated_sum = 0;
            if($request->get("separated") != null)
            {
                foreach ( Input::get("separated") as $id => $count )
                {
                    if( $count > 0 )
                        $separated_sum += $count;
                }
            }
            
            if ($separated_sum != $request->get("totalSeparated")) {
                $validator->errors()->add('totalSeparated', 'مجموع المفصول غير صحيح, المجموع هو ' . $separated_sum);
            }
        });
        
        //validate arrival sum
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $arrival_sums =  $inputs['rotated'] + $inputs['pretencesArrival'] + $inputs['arriving'] + $inputs['arrivalDirectComplaint'] + $inputs['eliminatedArrival'];
            
            if ($arrival_sums != $request->get("totalCases")) {
                $validator->errors()->add('totalCases', 'المجموع العام غير صحيح, المجموع هو ' . $arrival_sums);
            }
        });
        
        //validate rotated cases
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $current_date = date('Y-m-d',strtotime($inputs['year'] . "/" .$inputs['month'] . "/01"));
            $previous_month = previousMonth($current_date);
            $previous_report = monthlyReport::where(['date' => $previous_month,'judge_court_id' => $inputs['judge_court_id'],'speciality_id' => $inputs['speciality_id']])->first();
            
            if ( $previous_report !== null && $previous_report->remainedCases != $inputs['rotated'] ) {
                $validator->errors()->add('rotated', 'عدد الدعاوى المتبقية من الشهر الماضي هو ' . $previous_report->remainedCases);
            }
        });
        
        //validate remained cases
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $remained = ($inputs['totalCases'] - $inputs['totalSeparated']);
            if ( $remained != $inputs['remainedCases'] && $remained > 0 ) {
                $validator->errors()->add('remainedCases', 'عدد الدعاوى المتبقية غير صحيح, الرقم هو ' . ($inputs['totalCases'] - $inputs['totalSeparated']));
            }
            else if($remained < 0)
            {
                $validator->errors()->add('remainedCases', 'عدد الدعاوى المتبقية غير صحيح يجب التأكد من مجموع المفصول و المجموع العام');
            }
        });
        
        //check if duplicate month
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $duplicated_report = monthlyReport::where(['judge_id' => $inputs['judge_id'], 'speciality_id' => $inputs['speciality_id'],'judge_court_id' => $inputs['judge_court_id'], 'month' => $inputs['month'], 'year' => $inputs['year']])->where('id','<>',$inputs['id'])->first();
            
            if ( null !== $duplicated_report ) 
            {
                $validator->errors()->add('month', 'تم إدخال هذا الجدول سابقاً, <a href="'.url('monthly_reports',$duplicated_report->id).'">إضغط هنا</a> للتأكّد');
            }
        });
        
        if ($validator->fails()) {
            return redirect("monthly_reports/create")
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $report = new monthlyReport($request->all());
        $report->created_by = Auth::user()->id;
        $report->save();
        
        $report_id = $report->id;
        
        $data = [];
        
        reportsSeparated::where('monthly_report_id', '=', $report_id)->delete();
        
        if($request->get("separated") != null)
            foreach ( Input::get("separated") as $id => $count )
            {
                if( $count > 0 )
                    $data[] = ["separated_id" => $id,"monthly_report_id" => $report_id,"count" => $count];
            }
        reportsSeparated::insert($data);
        
        return redirect("monthly_reports/".$report_id);
    }
    
    public function bulkSave(monthlyReportsBulkRequest $request)
    {
        $input = Input::get();
        
        $main_info = array();
        $count = $input['count'];
        $main_info['judge_court_id'] = $input['judge_court_id'];
        $main_info['judge_id'] = $input['judge_id'];
        $main_info['year'] = $input['year'];
        $main_info['month'] = $input['month'];
        $specialities = Speciality::Lists("title", "id");
        $field_to_hide = [];
        
        //delete old records
        $temp_judge_court = $input['original_judge_court_id'] > 0 ? $input['original_judge_court_id'] : $input['judge_court_id'];
        $temp_month = $input['original_month'] > 0 ? $input['original_month'] : $input['month'];
        $temp_year = $input['original_year'] > 0 ? $input['original_year'] : $input['year'];
        
        $saved_reports = monthlyReport::where(['judge_court_id'=>$temp_judge_court,'month'=>$temp_month,'year'=>$temp_year])->lists('id','id');
        $saved_separated = reportsSeparated::whereIn('monthly_report_id', $saved_reports)->delete();
        monthlyReport::where(['judge_court_id'=>$temp_judge_court,'month'=>$temp_month,'year'=>$temp_year])->delete();
        
        $validator = Validator::make($request->all(),[]);
        $specialities_count = [];
        $all_records = [];
        
        foreach ( $input as $key => $value )
        {
            if(is_array($value))
            {
                $count = count($value);
            }
        }
        
        for($i = 0; $i < $count; $i++)
        {
            $single_record = array();
            $full_record = array();
            
            foreach ( $input as $key => $value )
            {
                if(is_array($value))
                {
                    $value = array_values($value);
                    $single_record[$key] = $value[$i];
                }
            }
            
            $full_record = array_merge($main_info, $single_record);
            $speciality_name = (isset ($specialities[$full_record["speciality_id"]])?$specialities[$full_record["speciality_id"]]:'');
            
            $validator->after(function($validator) use ($full_record,$i,$speciality_name) {
                if(isset($full_record['separated'])){
                    $separated_sum = 0;
                    if(count($full_record['separated'])>0)
                        foreach ( $full_record['separated'] as $id => $count )
                            if( $count > 0 )
                                $separated_sum += $count;
                    
                    if ($separated_sum != $full_record['totalSeparated'])
                        $validator->errors()->add('totalSeparated.'.$i, 'مجموع المفصول غير صحيح, المجموع هو <b>' . $separated_sum . '</b> لإختصاص '. $speciality_name);
                }
            });
            
            //validate arrival sum
            $validator->after(function($validator) use ($full_record,$i,$speciality_name) {
                $arrival_sums =  $full_record['rotated'] + $full_record['pretencesArrival'] + $full_record['arriving'] + $full_record['arrivalDirectComplaint'] + $full_record['eliminatedArrival'];

                if ($arrival_sums != $full_record["totalCases"]) {
                    $validator->errors()->add('totalCases.'.$i, 'المجموع العام غير صحيح, المجموع هو <b>' . $arrival_sums . '</b> لإختصاص '. $speciality_name);
                }
            });
            
            //validate rotated cases
            $validator->after(function($validator) use ($full_record,$i,$speciality_name) {
                $current_date = date('Y-m-d',strtotime($full_record['year'] . "/" .$full_record['month'] . "/01"));
                $previous_month = previousMonth($current_date);
                $previous_report = monthlyReport::where(['date' => $previous_month,'judge_court_id' => $full_record['judge_court_id'],'speciality_id' => $full_record['speciality_id']])->first();
                
                if ( $previous_report !== null && $previous_report->remainedCases != $full_record['rotated'] ) {
                    $validator->errors()->add('rotated.'.$i, 'عدد الدعاوى المتبقية من الشهر الماضي هو <b>' . $previous_report->remainedCases . '</b> لإختصاص '. $speciality_name);
                }
            });
            
            //validate remained cases
            $validator->after(function($validator) use ($full_record,$i,$speciality_name) {
                $remained = ($full_record['totalCases'] - $full_record['totalSeparated']);
                
                if ( $remained != $full_record['remainedCases'] && $remained > 0 ) {
                    $validator->errors()->add('remainedCases.'.$i, 'عدد الدعاوى المتبقية غير صحيح, الرقم هو ' . ($full_record['totalCases'] - $full_record['totalSeparated']));
                }
                else if($remained < 0)
                {
                    $validator->errors()->add('remainedCases.'.$i, 'عدد الدعاوى المتبقية غير صحيح يجب التأكد من مجموع المفصول و المجموع العام<b>' . $remained . '</b> لإختصاص '. $speciality_name);
                }
            });
            
            //validate specialities
            $validator->after(function($validator) use ($full_record,$i,$speciality_name) {
                $speciality_id = $full_record["speciality_id"];
                
                if($speciality_id == 0)
                {
                    $validator->errors()->add('speciality_id.'.$i, 'يجب تحديد الإختصاص في السجل رقم<b> ' . ($i + 1) . '</b>');
                }
            });
            
            //validate duplicated specialities
            $speciality_id = $full_record["speciality_id"];
            if(isset($specialities_count[$speciality_id]))
                $specialities_count[$speciality_id]++;
            else
                $specialities_count[$speciality_id] = 1;
            
            $all_records[] = $full_record;
        }
        
        //validate duplicated specialities
        $validator->after(function($validator) use ($i,$specialities_count,$specialities) {
            $i = 0;
            
            foreach ($specialities_count as $index => $speciality_count)
            {
                if($speciality_count > 1)
                {
                    $validator->errors()->add('speciality_id.'.$index, 'الإختصاص مكرر <b> ' . $specialities[$index] . '</b>');
                }
                $i++;
            }
        });

        if ($validator->fails()) {
            return redirect("monthly_reports/bulk_create")->withErrors($validator)->withInput()->with("field_to_hide");
        }
        
        foreach ($all_records as $single_record)
        {
            $this->SaveSingleRecord($single_record);
        }
        
        return redirect("monthly_reports/" . $input['judge_court_id'] . "/" . $input['month'] . "/" . $input['year']);
    }
    
    public function show($id)
    {
        $page_title = 'الجداول الشهرية';
        $field_to_hide = [];
        $monthly_report = monthlyReport::where("id",$id)->first();
        $fields = monthlyReport::find($id)->judgeCourt->Court->courtName->Fields;
        $separated = Separated::lists("title","id");
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $judge_courts = judgeCourt::all();
        $courts = Court::lists("title","id");
        $specialities = Speciality::Lists("title", "id");
        $separated_reports = reportsSeparated::where('monthly_report_id', '=', $id)->lists("count","separated_id");
        $judge_court = judgeCourt::with("Court")->where('id',$monthly_report->judge_court_id)->first();
        $court_name = Name::where('id',$judge_court->Court->court_name_id)->first();
        $court_type = Type::where('id',$judge_court->Court->court_type_id)->first();
        
        $field_to_hide = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
        
        return view('manage_monthly_report/manage_monthly_report', compact('page_title','monthly_report','fields','separated','judges','judge_courts','judge_courts','judge_courts','courts','specialities','separated_reports','field_to_hide'));
    }
    
    public function destroy($id)
    {
        //monthlyReport::find($id)->delete();
        return redirect("monthly_reports");
    }
    
    public function SaveSingleRecord($record)
    {
        $report = new monthlyReport($record);
        $report->created_by = Auth::user()->id;
        $report->save();
        
        $report_id = $report->id;
        
        $data = [];
        
        reportsSeparated::where('monthly_report_id', '=', $report_id)->delete();
        
        if(isset($record["separated"]) && !empty($record["separated"]))
            foreach ( $record["separated"] as $id => $count )
            {
                if( $count > 0 )
                    $data[] = ["separated_id" => $id,"monthly_report_id" => $report_id,"count" => $count];
            }
            
        reportsSeparated::insert($data);
        
        return redirect("monthly_reports");
    }
    
    public function update(monthlyReportsRequest $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($request->all(),[]);
        //validate separated cases
        $validator->after(function($validator) use ($request) {
            $separated_sum = 0;
            if($request->get("separated") != null)
            {
                foreach ( Input::get("separated") as $id => $count )
                {
                    if( $count > 0 )
                        $separated_sum += $count;
                }
            }
            
            if ($separated_sum != $request->get("totalSeparated")) {
                $validator->errors()->add('totalSeparated', 'مجموع المفصول غير صحيح, المجموع هو ' . $separated_sum);
            }
        });
        
        //validate arriving sum
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $arrival_sums =  $inputs['rotated'] + $inputs['pretencesArrival'] + $inputs['arriving'] + $inputs['arrivalDirectComplaint'] + $inputs['eliminatedArrival'];
            
            if ($arrival_sums != $request->get("totalCases")) {
                $validator->errors()->add('totalCases', 'المجموع العام غير صحيح, المجموع هو ' . $arrival_sums);
            }
        });
        
        //validate rotated cases
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $current_date = date('Y-m-d',strtotime($inputs['year'] . "/" .$inputs['month'] . "/01"));
            $previous_month = previousMonth($current_date);
            $previous_report = monthlyReport::where(['date' => $previous_month,'judge_court_id' => $inputs['judge_court_id'],'speciality_id' => $inputs['speciality_id']])->first();
            
            if ( $previous_report !== null && $previous_report->remainedCases != $inputs['rotated'] ) {
                $validator->errors()->add('rotated', 'عدد الدعاوى المتبقية من الشهر الماضي هو ' . $previous_report->remainedCases);
            }
        });
        
        //validate remained cases
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $remained = ($inputs['totalCases'] - $inputs['totalSeparated']);
            if ( $remained != $inputs['remainedCases'] && $remained > 0 ) {
                $validator->errors()->add('remainedCases', 'عدد الدعاوى المتبقية غير صحيح, الرقم هو ' . ($inputs['totalCases'] - $inputs['totalSeparated']));
            }
            else if($remained < 0)
            {
                $validator->errors()->add('remainedCases', 'عدد الدعاوى المتبقية غير صحيح يجب التأكد من مجموع المفصول و المجموع العام');
            }
        });
        
        //check if duplicate month
        $validator->after(function($validator) use ($request) {
            $inputs = $request->all();
            $duplicated_report = monthlyReport::where(['speciality_id' => $inputs['speciality_id'], 'judge_court_id' => $inputs['judge_court_id'], 'month' => $inputs['month'], 'year' => $inputs['year']])->where('id','<>',$inputs['id'])->first();
            
            if ( null !== $duplicated_report ) 
            {
                $validator->errors()->add('month', 'تم إدخال هذا الجدول سابقاً, <a href="'.url('monthly_reports',$duplicated_report->id).'">إضغط هنا</a> للتأكّد');
            }
        });
        
        if ($validator->fails()) {
            return redirect("monthly_reports/".$inputs['id'])
                        ->withErrors($validator)
                        ->withInput();
        }        
        
        $report_id = $request->get('id');
                
        $report = monthlyReport::findOrNew ($report_id);
        $report->fill($request->all());
        $report->modified_by = Auth::user()->id;
        $report->save();
        
        $report_id = $report->id;
        
        $data = [];
        
        reportsSeparated::where('monthly_report_id', '=', $report_id)->delete();
        
        if(Input::get("separated") != null)
            foreach ( Input::get("separated") as $id => $count )
            {
                if( $count > 0 )
                    $data[] = ["separated_id" => $id,"monthly_report_id" => $report_id,"count" => $count];
            }
        reportsSeparated::insert($data);
        
        return redirect("monthly_reports/".$report_id);
    }
    
    public function duplicate($id)
    {
        $report = monthlyReport::findOrNew($id);
        $newMonthlyReport = $report->replicate();
        $newMonthlyReport->save();
        
        $data = [];
        
        $separated_fields = reportsSeparated::where('monthly_report_id', '=', $id)->get();
        foreach ( $separated_fields as $separated_field )
        {
            if( $separated_field->count > 0 )
                $data[] = ["separated_id" => $separated_field->separated_id,"monthly_report_id" => $newMonthlyReport->id,"count" => $separated_field->count];
        }
        reportsSeparated::insert($data);
        
        return redirect("monthly_reports/" . $newMonthlyReport->id);
    }
    
    public function RefreshFields()
    {
        if (Session::token() !== Input::get('_token'))
        {
            return "";
        }

        $judge_court_id = Input::get("judge_court_id");
        $monthly_report_id = Input::get("monthly_report_id");
        $separated = Separated::lists("title","id");
        if($monthly_report_id > 0)
            $separated_reports = reportsSeparated::where('monthly_report_id', '=', $monthly_report_id)->lists("count","separated_id");
        
        $fields = judgeCourt::find($judge_court_id)->Court->courtName->Fields;
        
        $html = "";
        $tabIndex = 18;
        
        foreach ($fields as $field)
        {
            if(isset($separated[$field->separated_id]))
            {
                $html .= "<div class='width_12'>";
                $html .= '<label class="text-nowrap">' . $separated[$field->separated_id] . '</label>';
                    $html .= '<div>';
                    $html .= FormFacade::text('separated['.$field->separated_id.']', isset($separated_reports[$field->separated_id])?$separated_reports[$field->separated_id]:old('separated['.$field->separated_id.']'), ['class' => 'form-control','tabindex' => $tabIndex]);
                    $html .= '</div>';
                $html .= '</div> ';
                $tabIndex++;
            }
        }
        
        $judge_court = judgeCourt::with("Court")->where('id',$judge_court_id)->first();
        $court_name = Name::where('id',$judge_court->Court->court_name_id)->first();
        $court_type = Type::where('id',$judge_court->Court->court_type_id)->first();
        $field_to_hide = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
        
        $response = array(
            'status' => 'success',
            'fields' => $html,
            'judge_id' => $judge_court->judge_id,
            'fields_to_hide' => $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'')
        );

        return Response::json($response);
    }
    
    public function RefreshFieldsBulk()
    {
        if (Session::token() !== Input::get('_token'))
        {
            return "";
        }

        $judge_court_id = Input::get("judge_court_id");
        $monthly_report_id = Input::get("monthly_report_id");
        $separated = Separated::lists("title","id");
        if($monthly_report_id > 0)
            $separated_reports = reportsSeparated::where('monthly_report_id', '=', $monthly_report_id)->lists("count","separated_id");
        
        $judge_court    = judgeCourt::with("Court")->where('id',$judge_court_id)->first();
        $fields         = courtFields::where("court_name_id",$judge_court->Court->court_name_id)->where("court_type_id",$judge_court->Court->court_type_id)->orderBy("order")->get();
       
        $td_html = "";
        $th_html = "";
        $tabIndex = 18;
        
        foreach ($fields as $field)
        {
            $th_html .= '<th><span>';
            $th_html .= $separated[$field->separated_id];
            $th_html .= '</th></span>';

            $td_html .= '<td>';
            $td_html .= FormFacade::text('separated[]['.$field->separated_id.']', isset($separated_reports[$field->separated_id])?$separated_reports[$field->separated_id]:old('separated['.$field->separated_id.']',0), ['class' => 'form-control noPad indexedField',  "autocomplete" => "off"]);
            $td_html .= '</td>';

            $tabIndex++;
        }
        
        $court_name_id = $judge_court->Court->court_name_id;
        $court_type_id = $judge_court->Court->court_type_id;
        
        $judge_court = judgeCourt::with("Court")->where('id',$judge_court_id)->first();
        $court_name = Name::where('id',$court_name_id)->first();
        $court_type = Type::where('id',$court_type_id)->first();
        $field_to_hide = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
        
        $specialities = monthlyReport::where("judge_court_id",$judge_court_id)->groupBy('judge_court_id','speciality_id')->orderby('id')->lists('speciality_id','speciality_id')->toArray();
        
        $response = array(
            'status' => 'success',
            'specialities' => implode(",", $specialities),
            'court_name' => $court_name_id,
            'court_type' => $court_type_id,
            'titles' => $th_html,
            'fields' => $td_html,
            'judge_id' => $judge_court->judge_id,
            'fields_to_hide' => $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'')
        );

        return Response::json($response);
    }
    
    public function data()
    {
        $input = Input::all();
        $time_range = GetRange();
        
        $monthly_reports = monthlyReport::select(DB::raw('monthly_reports.id,courts.title as court,judge_court_id,CONCAT(judges.first_name," ",judges.last_name) as judge,year,month'))
                ->where(function($query) use ($time_range) {
                    $query->where('month', '>', 8)
                            ->where('month', '<', 13)
                            ->where('year', '=', date('Y',  strtotime($time_range['from'])))
                            ->orwhere(function($query) use ($time_range) {
                            $query->where('month', '>', 0)
                                  ->where('month', '<', 9)
                                  ->where('year', '=', date('Y',  strtotime($time_range['to'])));
                            });
                })
                ->join("specialities","specialities.id","=","speciality_id")
                ->join("judges","judges.id","=","judge_id")
                ->join("judge_courts","judge_courts.id","=","judge_court_id")
                ->join("courts",function($join){
                    $join->on("courts.id","=","judge_courts.court_id");
                    $join->whereIn("courts.province_id", explode(",", Session::get('provinces')) );
                })
                ->groupBy("year","month","judge_court_id")
                ->get();
        
        $reports_array = [];
        
        foreach ($monthly_reports as $index => $monthly_report)
        {
            $reports_array[$index] = $monthly_report->toArray();
            $reports_array[$index]['action'] = '<a href="' . url('monthly_reports/'.$monthly_report["judge_court_id"]."/".$monthly_report["month"]."/".$monthly_report["year"]) . '" class="btn btn-flat btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>';;
            unset($reports_array[$index]['id']);
            unset($reports_array[$index]['judge_court_id']);
        }
        
        $collection = new Collection($reports_array);
        
        return Datatables::of($collection)->make();
    }
    
    public function FieldsToHide($court_name, $court_type)
    {
        $fields = [];
        
        switch ($court_name)
        {
            case 'قاضي منفرد':
                if( $court_type == "مدني" )
                    $fields = ['pretencesArrival','arrivalDirectComplaint','forExecution','executed','primaryReport','protectionMeasures'];
                else if( $court_type == "جزائي" )
                    $fields = ['pretencesArrival','arrivalDirectComplaint','eliminatedArrival','primaryReport','protectionMeasures','protectionMeasures'];
                break;
            case 'محكمة إستئناف':
                if( $court_type == "مدني" )
                    $fields = ['pretencesArrival','arrivalDirectComplaint','forExecution','executed','primaryReport','protectionMeasures'];
                else if( $court_type == "جزائي" )
                    $fields = ['pretencesArrival','arrivalDirectComplaint','eliminatedArrival','primaryReport','protectionMeasures'];
                break;
            case 'غرفة بداية':
                $fields = ['pretencesArrival','arrivalDirectComplaint','forExecution','executed','primaryReport','protectionMeasures'];
                break;
            case 'هيئة إتهامية':
                $fields = ['pretencesArrival','arrivalDirectComplaint','eliminatedArrival','casesOnSchedule','forExecution','executed','primaryReport','protectionMeasures'];
                break;
            case 'جنايات':
                $fields = ['pretencesArrival','arrivalDirectComplaint','eliminatedArrival','primaryReport','protectionMeasures'];
                break;
            case 'جنايات أحداث':
                $fields = ['pretencesArrival','arrivalDirectComplaint','primaryReport','protectionMeasures'];
                break;
            case 'دائرة تحقيق':
                $fields = ['casesOnSchedule','arriving','eliminatedArrival','forExecution','executed','primaryReport','protectionMeasures'];
                break;
            case 'مجلس عمل تحكيمي':
                $fields = ['executed','arrivalDirectComplaint','forExecution','pretencesArrival','primaryReport','protectionMeasures'];
                break;
            case 'تمييز':
                $fields = ['pretencesArrival','arrivalDirectComplaint','eliminatedArrival','casesOnSchedule','forExecution','executed','protectionMeasures'];
                break;
            case 'نيابة عامة':
                $fields = [];
                break;
            case 'دائرة التنفيذ':
                $fields = ['casesOnSchedule','pretencesArrival','arrivalDirectComplaint','eliminatedArrival','forExecution','executed','primaryReport','protectionMeasures'];
                break;
            case 'جنح أحداث':
                $fields = ['pretencesArrival','arrivalDirectComplaint','eliminatedArrival','primaryReport'];
                break;
            default :
                $fields = [];
        }
        
        return $fields;
    }
    
    public function swap()
    {
//        dd(Request::segment(2));
        $validator = Validator::make([],[]);
        $page_title = "مبادلة الأحكام و الجداول";
                
        $input = Input::all();
        
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $judge_courts = judgeCourt::all();
        $courts = Court::lists("title","id");
        
        if(!empty($input))
        {
            $from               = $input["from"];
            $from_date          = $input["from_date"];
            $to                 = $input["to"];
            $to_date            = $input["to_date"];
            $from_judge_court   = judgeCourt::where('id',$from)->get();
            $to_judge_court     = judgeCourt::where('id',$to)->get();
            
            if(empty($from)) $validator->errors()->add('from', 'يجب تحديد المحكمة التي يجب نقل الأحكام منها');
            if(empty($to)) $validator->errors()->add('to', 'يجب تحديد المحكمة التي يجب نقل الأحكام إليها');
            if(empty($from_date)) $validator->errors()->add('from_date', 'يجب تحديد تاريخ الجدول من المحكمة المصدر');
            if(empty($to_date)) $validator->errors()->add('to_date', 'سجب تحديد تاريخ الجدول في المحكمة الجديدة');

            $monthly_reports = monthlyReport::where("judge_court_id",$from)
                    ->where("month",  Carbon::createFromFormat("Y-m-d",$from_date)->month )
                    ->where("year", Carbon::createFromFormat("Y-m-d",$from_date)->year )->get();
            
            $judgements = Judgement::where("judge_court_id",$from)
                    ->where("report_date", $from_date)->get();
            
            foreach($monthly_reports as $monthly_report)
            {
                $monthly_report->judge_court_id = $to;
                $monthly_report->judge_id = $to_judge_court[0]->judge_id;
                $monthly_report->month = Carbon::createFromFormat("Y-m-d",$to_date)->month;
                $monthly_report->year = Carbon::createFromFormat("Y-m-d",$to_date)->year;
                
                $monthly_report->save();
            }
            
            foreach($judgements as $judgement)
            {
                $judgement->judge_court_id = $to;
                $judgement->judge_id = $to_judge_court[0]->judge_id;
                $judgement->report_date = $to_date;
                
                $judgement->save();
            }
            
            return redirect('monthly_reports/'. $to .'/'. Carbon::createFromFormat("Y-m-d",$to_date)->month .'/'. Carbon::createFromFormat("Y-m-d",$to_date)->year);
        }
        
        return view('manage_monthly_report/swap',  compact('page_title','judges','courts','judge_courts','from','from_date','to','to_date'))->withErrors($validator);
    }
}
