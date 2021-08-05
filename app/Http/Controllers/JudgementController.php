<?php

namespace App\Http\Controllers;

use App\Article;
use App\Court;
use App\Http\Controllers\Controller;
use App\Http\Requests\judgmentBulkRequest;
use App\Http\Requests\judgmentRequest;
use App\judge;
use App\judgeCourt;
use App\Judgement;
use App\judgmentArticles;
use App\judgmentType;
use App\monthlyReport;
use App\Name;
use App\Speciality;
use App\Statuses;
use App\Type;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Html\FormFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class JudgementController extends Controller
{
    public function index()
    {
        $page_title = "عرض الأحكام التفصيلية";
        return view('manage_judgement/list_judgements', compact('page_title'));
    }
    
    public function create()
    {
        $old = Input::old();
        $page_title = "الأحكام التفصيلية";
        
        $dates = [];
        if(isset($old['judge_court_id']))
        {
            $monthly_reports = monthlyReport::where("judge_court_id", $old['judge_court_id'])->orderBy("month")->orderBy("year")->groupBy("date")->get();
            foreach ($monthly_reports as $monthly_report)
            {
                $date = date("Y-m-d", strtotime($monthly_report->year . "/" . $monthly_report->month . "/01" ));
                $dates[$date] = $date;
            }
        }
        
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $courts = Court::lists("title", "id");
        $judge_courts = judgeCourt::all();
        $specialities = Speciality::orderBy("title")->lists("title", "id");
        $statuses = Statuses::lists("title", "id");
        $articles = Article::all();
        $judgement_types = judgmentType::orderby("name")->lists("name", "id");
        $field_to_hide = [];
        
        if( isset($old['judge_court_id']) && !empty($old['judge_court_id']) )
        {
            $court_judge = judgeCourt::find($old['judge_court_id']);
            $court = Court::where('id',$court_judge->court_id)->first();
            $court_name = Name::where('id',$court->court_name_id)->first();
            $court_type = Type::where('id',$court->court_type_id)->first();
            $field_to_hide = $this->FieldsToHide($court_name->name,$court_type->type);
        }
        
        $field_to_hide = array_map(function($e) use($field_to_hide){
            return "hide_" . $e;
        }, $field_to_hide);
                
        $field_to_hide = implode(" ", $field_to_hide);
        
        return view('manage_judgement/manage_judgement', compact('page_title', 'dates', 'judges', 'courts', 'judge_courts', 'specialities', 'statuses', 'articles', 'judgement_types','field_to_hide'));
    }
    
    public function bulkDelete($judge_court_id,$month,$year)
    {
        $report_date = Carbon::create($year, $month, 1)->toDateString();
        //get all saved judgements for this month
        $judgement_ids = Judgement::where(['report_date'=>$report_date,'judge_court_id'=>$judge_court_id])->lists('id','id');
        //delete articles attached to all judgements
        judgmentArticles::whereIn('judgement_id', $judgement_ids)->delete();
        //delete judgments for this month
        Judgement::where(['report_date'=>$report_date,'judge_court_id'=>$judge_court_id])->delete();
        
        return redirect('manage_judgement/'. $judge_court_id .'/'. $month .'/'. $year);
    }
    
    public function bulkCreate()
    {
        $old = Input::old();
        $page_title = "الأحكام التفصيلية";
        $defaults       = ['judge_court_id'=>Session::get('judge_court_id'),'report_date'=>  Carbon::create(Session::get('year'), Session::get('month'), 1)->toDateString()];
        $last_report = Judgement::where("created_by", Auth::user()->id)->orderBy("created_at","desc")->limit(1)->get();
        
        if(isset($defaults) && $defaults["judge_court_id"]!=0 && Session::get('year')!=0 && Session::get('month')!=0)
        {
            $separated_count = DB::select(DB::raw("SELECT SUM(`totalSeparated`) as count "
                    . "FROM `monthly_reports` "
                    . "WHERE `judge_court_id`=".$defaults["judge_court_id"]." and month=".Session::get('month')." and year=".Session::get('year')));

            $total_separated = $separated_count[0]->count;
        }
        
        $dates = [];
        if(isset($old['judge_court_id']))
        {
            $monthly_reports = monthlyReport::where("judge_court_id", $old['judge_court_id'])->orderBy("month")->orderBy("year")->groupBy("date")->get();
            foreach ($monthly_reports as $monthly_report)
            {
                $date = date("Y-m-d", strtotime($monthly_report->year . "/" . $monthly_report->month . "/01" ));
                $dates[$date] = $date;
            }
        }
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $courts = Court::lists("title", "id");
        $judge_courts   = judgeCourt::select("judge_courts.id","judge_courts.court_id","judge_courts.judge_id")
                ->join('courts', 'courts.id', '=', 'court_id')
                ->whereIn('Courts.province_id', explode(",", Session::get('provinces')))
                ->get();
        $specialities = Speciality::orderBy("title")->lists("title", "id");
        $statuses = Statuses::lists("title", "id");
        $articles = Article::all();
        $judgement_types = judgmentType::orderby("name")->lists("name", "id");
        $field_to_hide = [];
        
        $dp_specialities = "<select name='speciality_id[]' data-style='btn-default' class='form-control selectpicker show-tick show-menu-arrow speciality_dp' data-live-search='true' data-size='10' title=' -- Select One -- '>";
            foreach($specialities as $i => $v):
                $dp_specialities .= "<option value='".$i."'>".$v."</option>";
            endforeach;
        $dp_specialities .= "</select>";
        
        $dp_judgments = "<select name='judgment_type_id[]' data-style='btn-default' class='form-control selectpicker show-tick show-menu-arrow speciality_dp' data-live-search='true' data-size='10' title=' -- Select One -- '>";
            foreach($judgement_types as $i => $v):
                $dp_judgments .= "<option value='".$i."'>".$v."</option>";
            endforeach;
        $dp_judgments .= "</select>";
        
        $dp_articles = '<select data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow" multiple  data-live-search="true" data-size=10 name="articles[{{$index}}][]" title=" -- Select One -- " data-actions-box="true">';
            foreach($articles as $article){
                $dp_articles .= '<option value="'.$article->id.'" data-content="<div class=\'label label-success\' style=\'display:inline-block\'>'.$article->number.' '.$article->name.'</div>">'.$article->number.' | '.$article->name.'</option>';
            }
        $dp_articles .= '</select>';
        
        $dp_judges = FormFacade::select('direct_judge_id[]', $judges, '', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default"]);
        
        if( isset($old['judge_court_id']) && !empty($old['judge_court_id']) )
        {
            $court_judge = judgeCourt::find($old['judge_court_id']);
            $court = Court::where('id',$court_judge->court_id)->first();
            $court_name = Name::where('id',$court->court_name_id)->first();
            $court_type = Type::where('id',$court->court_type_id)->first();
            $field_to_hide = $this->FieldsToHide($court_name->name,$court_type->type);
        }
        
        $field_to_hide = array_map(function($e) use($field_to_hide){
            return "hide_" . $e;
        }, $field_to_hide);
                
        $field_to_hide = implode(" ", $field_to_hide);
        
        return view('manage_judgement/manage_judgement_bulk', compact('page_title', 'dates', 'judges', 'courts', 'judge_courts', 'specialities', 'statuses', 'articles', 'judgement_types','field_to_hide','defaults','dp_articles','dp_judgments','dp_specialities','dp_judges','last_report','total_separated'));
    }
    
    public function show($id)
    {
        $page_title = "الأحكام التفصيلية";
        $judgement = Judgement::where("id", $id)->first();
        
        $monthly_reports = monthlyReport::where("judge_court_id", $judgement->judge_court_id)->orderBy("month")->orderBy("year")->groupby("month")->groupby("year")->get();
        
        $dates = [];
        foreach ($monthly_reports as $monthly_report)
        {
            $date = date("Y-m-d", strtotime($monthly_report->year . "/" . $monthly_report->month . "/01" ));
            $dates[$date] = $date;
        }
        
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $courts = Court::lists("title", "id");
        $judge_courts = judgeCourt::all();
        $specialities = Speciality::orderBy("title")->lists("title", "id");
        $statuses = Statuses::lists("title", "id");
        $articles = Article::all();
        $judgment_articles = judgmentArticles::where("judgement_id", $id)->lists("judgement_id", "articles_id");
        $judgement_types = judgmentType::orderby("name")->lists("name", "id");
        
        $court_judge = judgeCourt::find($judgement->judge_court_id);
        $court = Court::where('id',$court_judge->court_id)->first();
        $court_name = Name::where('id',$court->court_name_id)->first();
        $court_type = Type::where('id',$court->court_type_id)->first();
        $field_to_hide = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
        
        $field_to_hide = array_map(function($e) use($field_to_hide){
            return "hide_" . $e;
        }, $field_to_hide);
                
        $field_to_hide = implode(" ", $field_to_hide);
        
        return view('manage_judgement/manage_judgement', compact('page_title', 'judgement', 'dates', 'judges', 'courts', 'judge_courts', 'specialities', 'statuses', 'articles', 'judgment_articles', 'judgement_types','field_to_hide'));
    }

    public function destroy($id)
    {
        Judgement::find($id)->delete();
        return redirect("manage_judgement");
    }
    
    public function store(judgmentRequest $request)
    {
        $judgment = new Judgement($request->all());
        $judgment->created_by = Auth::user()->id;
        $judgment->save();

        $row_id = $judgment->id;

        //save judgement articles
        $articles = Input::get("articles");
        $data = [];

        judgmentArticles::where("judgement_id", $row_id)->delete();

        if (count($articles) > 0)
            foreach ($articles as $article)
            {
                $data[] = ["articles_id" => $article, "judgement_id" => $row_id];
            }

        judgmentArticles::insert($data);

        return redirect("manage_judgement/".$row_id);
    }
    
    public function bulkSave(judgmentBulkRequest $request)
    {
        $input = Input::all();
        
        //$start_time = microtime(true);
        $all_records = array();
        $for_insert = array();
        $main_info = array(
            "judge_court_id" => $input["judge_court_id"],
            "report_date" => $input["report_date"],
            "judge_id" => $input["judge_id"]
        );
        
        $count = $input["count"];
        $row_ids = $input["row_id"];

        $month = date('m',  strtotime($main_info["report_date"]));
        $year = date('Y', strtotime($main_info["report_date"]));
        
        $temp_judge_court = $input['original_judge_court_id'] > 0 ? $input['original_judge_court_id'] : $input['judge_court_id'];
        //get all saved judgements for this month
        $judgement_ids = Judgement::where(['report_date'=>$input["report_date"],'judge_court_id'=>$temp_judge_court])->lists('id')->toArray();
        
        $update_ids = array_intersect($judgement_ids, $row_ids);
        $delete_ids = array_diff($judgement_ids, $row_ids);
        
        //delete articles attached to all judgements
        judgmentArticles::whereIn('judgement_id', $judgement_ids)->delete();
        
        //remove judgments that needs to be deleted
        Judgement::whereIn('id',$delete_ids)->delete();
        
        for($i = 0; $i < $count; $i++)
        {
            $single_record = array();
            $full_record = array();
            
            foreach ( $input as $key => $value )
                if(is_array($value) && isset($value[$i]))
                    $single_record[$key] = $value[$i];
            
            $full_record = array_merge($main_info, $single_record);
            
            if( !empty($full_record["direct_judge_id"]) )
            {
                $full_record["judge_id"] = $full_record["direct_judge_id"];
                unset($full_record["direct_judge_id"]);
            }
            
            $full_record = array_merge(["id"=>$full_record["row_id"]], $full_record);
            $full_record["created_by"] = Auth::user()->id;
            unset($full_record["row_id"]);
            $all_records[] = $full_record;
        }
        
        
        foreach ($all_records as $single_record)
        {
            $this->SaveSingleRecord($single_record);
        }
        //$end_time = microtime(true);
        //dd( $end_time - $start_time );
        return redirect("manage_judgement/".$main_info["judge_court_id"]."/".$month."/".$year);
    }

    public function SaveSingleRecord($judements)
    {
        $articles = isset($judements["articles"])?$judements["articles"]:[];
        $id = isset($judements["id"])?$judements["id"]:[];
        
        unset($judements["articles"]);
        unset($judements["id"]);
        
        $judgment = Judgement::updateOrCreate(["id"=>$id],$judements);

        $row_id = $judgment->id;

        $data = [];
        
        if(count($articles) > 0)
        {
            if(is_array($articles))
            {
                foreach($articles as $article)
                {
                    $data[] = ["articles_id" => $article, "judgement_id" => $row_id];
                }
            }
            else
            {
                $data[] = ["articles_id" => $articles, "judgement_id" => $row_id];
            }
        }
        
        judgmentArticles::insert($data);
        
        return ["id" => $row_id,"data" => $judements];
    }
    
    public function DeleteJudgmentRecord()
    {
        $id = Input::get('id');
        $resp = Judgement::destroy($id);
        
        return Response::json(["is_error" => !$resp]);
    }
    
    public function showBulk($judge_court_id, $month, $year)
    {
        $start_time = microtime(true);
        $time_range = GetRange();
        $old = Input::old();
        $page_title = "الأحكام التفصيلية";
        $report_date = Carbon::create($year, $month, 1)->toDateString();
        $judgements = Judgement::where(["judge_court_id"=>$judge_court_id,"report_date"=>$report_date])->orderBy('rule_number', 'asc')->get();
        $last_report = Judgement::where("created_by", Auth::user()->id)->orderBy("created_at","desc")->limit(1)->get();
        //dd($report_date);
        $count = $judgements->count();
        $rows_per_page = 20;
        
        if($count == 0)
            return redirect("manage_judgement/bulk_create")->with(['judge_court_id'=>$judge_court_id,'month'=>$month,'year'=>$year]);
        
        $monthly_reports = monthlyReport::where("judge_court_id", $judge_court_id)->orderBy("month")->orderBy("year")->groupby("month")->groupby("year")
                        ->where(function($query) use ($time_range) {
                            $query->where('month', '>', 8)
                                  ->where('month', '<', 13)
                                  ->where('year', '=', date('Y',  strtotime($time_range['from'])));
                        })
                        ->orwhere(function($query) use ($time_range) {
                            $query->where('month', '>', 0)
                                  ->where('month', '<', 9)
                                  ->where('year', '=', date('Y',  strtotime($time_range['to'])));
                        })->get();
        
        $separated_count = DB::select(DB::raw("SELECT SUM(`totalSeparated`) as count "
                . "FROM `monthly_reports` "
                . "WHERE `judge_court_id`=".$judge_court_id." and month=".$month." and year=".$year));
        
        $total_separated = $separated_count[0]->count;
        
        $dates = [];
        foreach ($monthly_reports as $monthly_report)
        {
            $date = date("Y-m-d", strtotime($monthly_report->year . "/" . $monthly_report->month . "/01" ));
            $dates[$date] = $date;
        }
        
        
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $courts = Court::lists("title", "id");
        $judge_courts   = judgeCourt::select("judge_courts.id","judge_courts.court_id","judge_courts.judge_id")
                ->join('courts', 'courts.id', '=', 'court_id')
                ->whereIn('Courts.province_id', explode(",", Session::get('provinces')))
                ->get();
        $specialities = Speciality::orderBy("title")->lists("title", "id");
        $statuses = Statuses::lists("title", "id");
        $articles = Article::all()->keyBy("id");
        $judgement_types = judgmentType::orderby("name")->lists("name", "id");
        
        $dp_specialities = "<select name='speciality_id[]' data-style='btn-default' class='form-control selectpicker show-tick show-menu-arrow speciality_dp' data-live-search='true' data-size='10' title=' -- Select One -- '>";
            foreach($specialities as $i => $v):
                $dp_specialities .= "<option value='".$i."'>".$v."</option>";
            endforeach;
        $dp_specialities .= "</select>";
        
        $dp_judgments = "<select name='judgment_type_id[]' data-style='btn-default' class='form-control selectpicker show-tick show-menu-arrow speciality_dp' data-live-search='true' data-size='10' title=' -- Select One -- '>";
            foreach($judgement_types as $i => $v):
                $dp_judgments .= "<option value='".$i."'>".$v."</option>";
            endforeach;
        $dp_judgments .= "</select>";
        
        $dp_articles = '<select data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow" multiple  data-live-search="true" data-size=10 name="articles[{{$index}}][]" title=" -- Select One -- " data-actions-box="true">';
            foreach($articles as $article){
                $dp_articles .= '<option value="'.$article->id.'" data-content="<div class=\'label label-success\' style=\'display:inline-block\'>'.$article->number.' '.$article->name.'</div>">'.$article->number.' | '.$article->name.'</option>';
            }
        $dp_articles .= '</select>';
        
        $dp_judges = FormFacade::select('direct_judge_id[]', $judges, '', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default"]);
        
        foreach ($judgements as $judgement)
            $judgment_articles[] = judgmentArticles::where("judgement_id", $judgement->id)->lists("articles_id", "articles_id")->toArray();
        
        $court_judge = judgeCourt::find($judge_court_id);
        $court = Court::where('id',$court_judge->court_id)->first();
        $court_name = Name::where('id',$court->court_name_id)->first();
        $court_type = Type::where('id',$court->court_type_id)->first();
        $field_to_hide = $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'');
        
        $field_to_hide = array_map(function($e) use($field_to_hide){
            return "hide_" . $e;
        }, $field_to_hide);
                
        $field_to_hide = implode(" ", $field_to_hide);
        
        return view('manage_judgement/manage_judgement_bulk', compact('page_title', 'judgements', 'dates', 'courts', 'judge_courts', 'statuses', 'judgment_articles','field_to_hide','count','old','judge_court_id', 'month', 'year','dp_articles','start_time','dp_judgments','dp_specialities','dp_judges','total_separated','last_report','rows_per_page', 
                'specialities','judgement_types','articles','judges'));
    }
    
    public function update(judgmentRequest $request)
    {
        $validator = Validator::make($request->all(),[]);
        
        //validate separated sums
        $validator->after(function($validator) use ($request) {
            $separated_sum = 0;
            
            $judge_court_id = $request->get("judge_court_id");
            $court_name = judgeCourt::find($judge_court_id)->Court->courtName->name;
            $judge_type = judgeCourt::find($judge_court_id)->Court->courtType->type;
            
            switch ( $court_name )
            {
                case 'قاضي منفرد':
                    if( $judge_type == "منفرد" )
                    {
                        if ($request->get("speciality_id") == "")
                            $validator->errors()->add('speciality_id', 'يجب تحديد نوع الحكم');
                    }
                    else if( $judge_type == "جزائي" )
                    {
                        if ($request->get("judgment_type_id") == "")
                            $validator->errors()->add('judgment_type_id', 'يجب تحديد نتيجة الحكم');
                    }
                    break;
                case 'محكمة إستئناف':
                    if( $judge_type == "منفرد" )
                    {
                        if ($request->get("speciality_id") == "")
                            $validator->errors()->add('speciality_id', 'يجب تحديد نوع الحكم');
                        if ($request->get("judgment_type_id") == "")
                            $validator->errors()->add('judgment_type_id', 'يجب تحديد نتيجة الحكم');
                    }
                    else if( $judge_type == "جزائي" )
                    {
                        if ($request->get("judgment_type_id") == "")
                            $validator->errors()->add('judgment_type_id', 'يجب تحديد نتيجة الحكم');
                    }
                    break;
                case 'غرفة بداية':
                case 'دائرة التنفيذ':
                        if ($request->get("speciality_id") == "")
                            $validator->errors()->add('speciality_id', 'يجب تحديد نوع الحكم');
                    break;
                case 'هيئة إتهامية':
                        if ($request->get("judgment_type_id") == "")
                            $validator->errors()->add('judgment_type_id', 'يجب تحديد نتيجة الحكم');
                    break;
            }
            
            if (date($request->get("arrival_date")) < date("1943-11-22"))
                $validator->errors()->add('arrival_date', 'تاريخ الورود غير صحيح');
            
            if ( !empty($request->get("last_session")) && date($request->get("last_session")) < date("1943-11-22"))
                $validator->errors()->add('last_session', 'تاريخ الجلسة الختامية غير صحيح');
            
            if (date($request->get("judgement_date")) < date("1943-11-22"))
                $validator->errors()->add('judgement_date', 'تاريخ الحكم غير صحيح');
            
            if (date($request->get("judgement_date")) < date($request->get("arrival_date")))
            {
                $validator->errors()->add('judgement_date', 'لا يمكن أن يكون تاريخ الورود أكبر من تاريخ الحكم');
                $validator->errors()->add('arrival_date', 'لا يمكن أن يكون تاريخ الحكم أقل من تاريخ الورود');
            }
            
            if (!empty($request->get("last_session")) && (date($request->get("last_session")) < date($request->get("arrival_date"))))
            {
                $validator->errors()->add('last_session', 'لا يمكن أن يكون تاريخ الجلسة الختامية أقل من تاريخ الورود');
            }
            
            if (!empty($request->get("last_session")) &&  (date($request->get("last_session")) > date($request->get("judgement_date"))))
            {
                $validator->errors()->add('last_session', 'لا يمكن أن يكون تاريخ صدور الحكم  أكبر من تاريخ الجلسة النهائية');
            }
        });
        
        //validate separated sums
        $validator->after(function($validator) use ($request) {
            $separated_sum = 0;
            
            $judge_court_id = $request->get("judge_court_id");
            $court_name = judgeCourt::find($judge_court_id)->Court->courtName->name;
            $judge_type = judgeCourt::find($judge_court_id)->Court->courtType->type;
            
            
        });
        
        if ($validator->fails()) {
            return redirect("manage_judgement/" . $request->get('id'))
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $row_id = $request->get("id");
        $judgement = Judgement::findOrNew($row_id);
        $judgement->fill($request->all());
        $judgement->modified_by = Auth::user()->id;
        $judgement->save();

        //save judgement articles
        $articles = $request->get("articles");

        $data = [];

        judgmentArticles::where("judgement_id", $row_id)->delete();

        if (count($articles) > 0)
            foreach ($articles as $article)
            {
                $data[] = ["articles_id" => $article, "judgement_id" => $row_id];
            }

        judgmentArticles::insert($data);

        return redirect("manage_judgement/" . $row_id);
    }

    public function duplicate($id)
    {
        $judgement = Judgement::findOrNew($id);
        $newJudgement = $judgement->replicate();
        $newJudgement->save();

        //save judgement articles
        $data = [];

        $articles = judgmentArticles::where("judgement_id", $id)->get();

        if ($articles->count() > 0)
        {
            foreach ($articles as $article)
            {
                $data[] = ["articles_id" => $article->articles_id, "judgement_id" => $newJudgement->id];
            }
            
            judgmentArticles::insert($data);
        }

        return redirect("manage_judgement/" . $newJudgement->id);
    }

    public function data()
    {
        $input = Input::all();
        $time_range = GetRange();
        
        $judgements = Judgement::selectRaw(DB::raw('judgements.id,judgements.judge_court_id,courts.title,'
                . 'CONCAT(judges.first_name," ",judges.last_name) as judge,report_date'))
                ->join("judges","judges.id","=","judge_id")
                ->join("judge_courts","judge_courts.id","=","judge_court_id")
                ->join("courts",function($join){
                    $join->on("courts.id","=","judge_courts.court_id");
                    $join->whereIn("courts.province_id", explode(",", Session::get('provinces')) );
                })
                ->whereBetween('report_date', $time_range)
                ->groupBy("judge_court_id","report_date")
                ->orderBy("report_date","ASC")
                ->get();
        
        $reports_array = [];
        
        foreach ($judgements as $index => $judgement)
        {
            $month = date('n',  strtotime($judgement['report_date']));
            $year = date('Y',  strtotime($judgement['report_date']));
            $reports_array[$index] = $judgement->toArray();
            $reports_array[$index]["action"] = '<a href="' . url('manage_judgement/' . $judgement["judge_court_id"]."/".$month."/".$year) . '" class="btn btn-flat btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>';
            unset($reports_array[$index]['judge_court_id']);
        }
        
        $collection = new Collection($reports_array);
        return Datatables::of($collection)->make();
    }
    
    
    public function SaveJudgmentRecord()
    {
        $main_data = Input::get('main_data');
        $record = Input::get('record');
        
        if( !checkIsAValidDate($main_data["report_date"]) )
            return Response::json(["is_error" => true,"id" => $record["row_id"]]);
        
        if( !$main_data["judge_court_id"] )
            return Response::json(["is_error" => true,"id" => $record["row_id"]]);
        
        
        $judgement = array_merge($main_data,$record);
        
        $judgement["id"] = $judgement["row_id"];
        $judgement["judge_id"] = (isset($judgement["direct_judge_id"]) && !empty($judgement["direct_judge_id"]))? $judgement["direct_judge_id"] : @$judgement["judge_id"];
        $judgement["created_by"] = Auth::user()->id;
        
        unset($judgement["direct_judge_id"]);
        unset($judgement["row_id"]);
        unset($judgement["_token"]);
        
        judgmentArticles::where('judgement_id', $judgement["id"])->delete();
        
        $resp = $this->SaveSingleRecord($judgement);
        
        return Response::json(["is_error" => false,"id" => $resp["id"]]);
    }
    
    public function RefreshJudgementForm()
    {
        if (Session::token() !== Input::get('_token'))
            return "";
        $time_range = GetRange();
        $judge_court_id = Input::get("judge_court_id");
        $report_date = Input::get("report_date");

        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $court_judge = judgeCourt::find($judge_court_id);
        
        $court = Court::where('id',$court_judge->court_id)->first();
        $court_name = Name::where('id',$court->court_name_id)->first();
        $court_type = Type::where('id',$court->court_type_id)->first();

        $monthly_reports = monthlyReport::where("judge_court_id", $judge_court_id)->orderBy("month")->orderBy("year")->groupby("month")->groupby("year")
                                ->where(function($query) use ($time_range) {
                                    $query->where('month', '>', 8)
                                          ->where('month', '<', 13)
                                          ->where('year', '=', date('Y',  strtotime($time_range['from'])))
                                          ->orwhere(function($query) use ($time_range) {
                                                $query->where('month', '>', 0)
                                                      ->where('month', '<', 9)
                                                      ->where('year', '=', date('Y',  strtotime($time_range['to'])));
                                            });
                                })->get();
        
        $dates = [];
        foreach ($monthly_reports as $monthly_report)
        {
            $date = date("Y-m-d", strtotime($monthly_report->year . "/" . $monthly_report->month . "/01" ));
            $dates[$date] = $date;
        }
        
        $dates_dp = FormFacade::select('report_date', $dates, $report_date, ['class' => 'form-control selectpicker show-tick show-menu-arrow', 'data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", "id" => "REPORT_DATE_FIELD", "data-uri" => url('manage_judgement')]);
        $judges_dp = FormFacade::select('judge_id', $judges, $court_judge->judge_id, ['class' => 'form-control selectpicker show-tick show-menu-arrow', 'data-live-search' => "true", 'data-size' => 10, "title" => ' -- Select One -- ']);

        $response = array(
            'status' => 'success',
            'dates_dp' => $dates_dp,
            'judges_dp' => $judges_dp,
            'fields_to_hide' => $this->FieldsToHide(isset($court_name->name)?$court_name->name:'',  isset($court_type->type)?$court_type->type:'')
        );
        
        return Response::json($response);
    }
    
    public function LoadSavedJudgements()
    {
        $judge_court_id = Input::get('judge_court_id');
        $monthly_report_id = Input::get('monthly_report_id');
        $judgment_articles = [];
        
        $judgements = Judgement::where(["judge_court_id"=>$judge_court_id,"report_date"=>$monthly_report_id])->orderBy('rule_number', 'asc')->get();
        $count = $judgements->count();
        
        foreach ($judgements as $judgement)
            $judgment_articles[] = judgmentArticles::where("judgement_id", $judgement->id)->lists("judgement_id", "articles_id");
        
        $response = array(
            'status' => 'success',
            'count' => $count,
            'judgements' => $judgements,
            'judgment_articles' => $judgment_articles
        );
        
        return Response::json($response);
    }
    
    public function FieldsToHide($court_name, $court_type)
    {
        $fields = [];
        switch ($court_name)
        {
            case 'قاضي منفرد':
                if( $court_type == "مدني" )
                    $fields = ['articles','judgment_type_id','decision_source','notes'];
                else if( $court_type == "جزائي" )
                    $fields = ['decision_source','status_id','speciality_id','notes'];
                break;
            case 'محكمة إستئناف':
                if( $court_type == "مدني" )
                    $fields = ['status_id','articles','notes'];
                else if( $court_type == "جزائي" )
                    $fields = ['status_id','notes','speciality_id','notes'];
                break;
            case 'غرفة بداية':
                $fields = ['articles','judgment_type_id','decision_source','notes'];
                break;
            case 'هيئة إتهامية':
                $fields = ['decision_source','status_id','speciality_id','notes'];
                break;
            case 'جنايات':
                $fields = ['decision_source','status_id','notes'];
                break;
            case 'جنايات أحداث':
                $fields = ['notes'];
                break;
            case 'دائرة تحقيق':
                $fields = ['notes'];
                break;
            case 'مجلس عمل تحكيمي':
                $fields = ['decision_source','articles','notes'];
                break;
            case 'تمييز':
                $fields = ['notes'];
                break;
            case 'نيابة عامة':
                $fields = ['notes'];
                break;
            case 'دائرة التنفيذ':
                $fields = ['articles','judgment_type_id','decision_source','status_id','notes'];
                break;
            case 'جنح أحداث':
                $fields = ['decision_source','notes'];
                break;
        }
        
        return $fields;
    }
}
