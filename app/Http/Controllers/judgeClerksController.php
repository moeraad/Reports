<?php

namespace App\Http\Controllers;

use App\Clerk;
use App\Court;
use App\Http\Controllers\Controller;
use App\Http\Requests\JudgeClerksRequest;
use App\judge;
use App\judgeClerk;

class judgeClerksController extends Controller
{
    function index()
    {
        $judge_clerks = judgeClerk::all();
        $judges = judge::orderBy("first_name")->get()->lists("full_name","id");
        $clerks = Clerk::orderBy('first_name')->get()->lists("full_name", "id");
        
        return view("judge_clerks/list_judge_clerks", compact("judge_clerks","clerks","judges"));
    }
    
    function create()
    {
        $clerks = Clerk::orderBy("first_name")->get()->lists("full_name","id");
        $judges = judge::orderBy("first_name")->get()->lists("full_name","id");
        
        return view("judge_clerks/manage_judge_clerks",  compact("clerks","judges"));
    }
    
    function store(JudgeClerksRequest $request)
    {
        $judge_clerk = judgeClerk::findOrNew($request->get("id"));
        $judge_clerk->fill($request->all());
        $judge_clerk->save();
        
        return redirect("judge_clerks/".$judge_clerk->id);
    }
    
    function update(JudgeClerksRequest $request)
    {
        $judge_clerk = judgeClerk::findOrNew($request->get("id"));
        $judge_clerk->fill($request->all());
        $judge_clerk->save();
        
        return redirect("judge_clerks/".$judge_clerk->id);
    }
    
    function show($id)
    {
        $judge_clerk = judgeClerk::where("id", $id)->first();
        $judges = judge::orderBy("first_name")->get()->lists("full_name","id");
        $clerks = Clerk::orderBy('first_name')->get()->lists("full_name", "id");
        
        return view("judge_clerks/manage_judge_clerks", compact("judge_clerk","judges","clerks"));
    }
    
    function destroy($id)
    {
        judgeClerk::where("id", $id)->delete();
        
        return redirect("judge_clerks");
    }
}
