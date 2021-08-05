<?php

namespace App\Http\Controllers;

use App\Advisor;
use App\Court;
use App\Http\Controllers\Controller;
use App\Http\Requests\judgeCourtRequest;
use App\judge;
use App\judgeCourt;
use App\Role;
use Illuminate\Support\Facades\Session;

class ManageJudgesCourtsController extends Controller
{
    public function index()
    {
        $page_title = "عرض قضاة المحاكم";
        $judge_courts = judgeCourt::all();
        $judges = judge::orderBy("first_name")->get()->lists("full_name", "id");
        $courts = Court::lists("title","id");
        $roles = Role::lists("title","id");
        
        return view('manage_judge_court/list_judge_court', compact('page_title','judge_courts','judges','courts','roles'));
    }
    
    public function create()
    {
        $page_title = "ربط قاضي بمحكمة";
        $judges = judge::orderBy("first_name")->get()->lists("full_name", "id");
        $courts = Court::whereIn("province_id",  explode(",", Session::get('provinces')))->lists("title","id");
        $roles = Role::lists("title","id");
        
        return view('manage_judge_court/manage_judge_court', compact('page_title','judges','courts','roles'));
    }
    
    public function copy($id)
    {
        $page_title = "ربط قاضي بمحكمة";
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $courts = Court::lists("title","id");
        $roles = Role::lists("title","id");
        $judge_court = judgeCourt::select('court_id','judge_id','role_id','date_from','date_to')->orderBy('id','desc')->where('id',$id)->first();
        
        return view('manage_judge_court/manage_judge_court', compact('judge_court','page_title','judges','courts','roles'));
    }
    
    public function show($id)
    {        
        $page_title = 'قضاة المحاكم';
        $judge_court = judgeCourt::where("id",$id)->first();
        $judges = judge::orderBy('first_name')->get()->lists("full_name", "id");
        $courts = Court::orderBy("title")->lists("title","id");
        $roles = Role::orderBy("title")->lists("title","id");
        $advisors = Advisor::where("judge_court_id", $id)->lists("judge_court_id", "judge_id");
        
        return view('manage_judge_court/manage_judge_court', compact('page_title','judge_court','judges','courts','roles','advisors'));
    }
    
    public function destroy($id)
    {
        judgeCourt::find($id)->delete();
        return redirect("manage_judge_court");
    }
    
    public function store(judgeCourtRequest $request)
    {
        $judge_court = new judgeCourt($request->all());
        $judge_court->save();
        
        $advisors = $request->get("advisors");
        if(isset($advisors))
            $this->saveAdvisors($advisors, $judge_court->id);
        
        return redirect("manage_judge_court/" . $judge_court->id);
    }
    
    public function update(judgeCourtRequest $request)
    {
        $judge_court_id = $request->get("id");
        $judge_court = judgeCourt::findOrNew($judge_court_id);
        $judge_court->fill($request->all());
        $judge_court->save();
        
        $advisors = $request->get("advisors");
        if(isset($advisors))
            $this->saveAdvisors($advisors, $judge_court->id);
        
        return redirect("manage_judge_court/" . $judge_court->id);
    }
    
    public function saveAdvisors($advisors,$row_id)
    {
        //save advisors
        $data = [];
        
        Advisor::where("judge_court_id", $row_id)->delete();
        
        foreach ( $advisors as $advisor )
        {
            $data[] = ["judge_id" => $advisor, "judge_court_id" => $row_id];
        }
        
        Advisor::insert($data);
    }
}
