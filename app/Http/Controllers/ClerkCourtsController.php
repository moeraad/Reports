<?php

namespace App\Http\Controllers;

use App\Clerk;
use App\ClerkCourts;
use App\Court;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClerkCourtRequest;
use Illuminate\Support\Facades\DB;

class ClerkCourtsController extends Controller
{
    function index()
    {
        $clerk_courts = ClerkCourts::selectRaw(DB::raw("id,court_id,GROUP_CONCAT(clerk_id) AS clerks_ids"))->get();
        
        $clerks = Clerk::all()->lists("full_name","id");
        $courts = Court::lists("title","id");
        
        return view("clerk_court/list_clerk_courts", compact("clerk_courts","clerks","courts"));
    }
    
    function show($id)
    {
        $clerk_court =  ClerkCourts::where("id", $id)->first();
        $court_clerks = ClerkCourts::where("court_id",$clerk_court->court_id)->lists("clerk_id")->all();
        
        $courts = Court::lists("title", "id");
        $clerks = Clerk::orderBy('first_name')->get()->lists("full_name", "id");
        
        return view("clerk_court/manage_clerk_courts", compact("clerk_court","court_clerks","courts","clerks"));
    }
    
    function create()
    {
        $courts = Court::lists("title", "id");
        $clerks = Clerk::orderBy('first_name')->get()->lists("full_name", "id");
        
        return view("clerk_court/manage_clerk_courts", compact("courts","clerks"));
    }
    
        
    function update(ClerkCourtRequest $request)
    {
        $clerks = $request->get("clerk_id");
        $court_id = $request->get("court_id");
        ClerkCourts::where("court_id",$court_id)->delete();
        
        foreach ($clerks as $clerk)
        {
            $clerk_court = new ClerkCourts();
            
            $clerk_court->court_id = $court_id;
            $clerk_court->clerk_id = $clerk;
            
            $clerk_court->save();
        }
        
        
        return redirect("clerk_courts/".$clerk_court->id);
    }
    
    function store(ClerkCourtRequest $request)
    {
        $clerks = $request->get("clerk_id");
        $court_id = $request->get("court_id");
        ClerkCourts::where("court_id",$court_id)->delete();
        
        foreach ($clerks as $clerk)
        {
            $clerk_court = new ClerkCourts();
            
            $clerk_court->court_id = $court_id;
            $clerk_court->clerk_id = $clerk;
            
            $clerk_court->save();
        }
        
        return redirect("clerk_courts/".$clerk_court->id);
    }
    
    public function destroy($id)
    {
        $clerk_court = ClerkCourts::where("id",$id)->first();
        ClerkCourts::where("court_id", $clerk_court->court_id)->delete();
        
        return redirect("clerk_courts");
    }
}