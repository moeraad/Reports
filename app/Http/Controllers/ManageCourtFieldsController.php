<?php

namespace App\Http\Controllers;

use App\courtFields;
use App\Http\Controllers\Controller;
use App\Http\Requests\courtFieldsRequest;
use App\Name;
use App\Separated;
use App\Type;

class ManageCourtFieldsController extends Controller
{
    public function show($id)
    {
        if(empty($id))
        {
            $id = Name::where("id",">",0)->min('id');
            if($id > 0)
                return redirect("manage_court_fields/" . $id);
        }
        
        $page_title = 'خانات المحكمة';
        $name = Name::where('id',$id)->first();
        $names = Name::lists('name','id');
        $types = Type::lists('type','id')->prepend("N/A",0);
        $court_fields = courtFields::where("court_name_id",$id)->lists("order","separated_id")->toArray();
        
        $separated = Separated::orderBy('title')->lists("title","id");
        
        return view('manage_court_fields/manage_court_fields', compact("page_title","court_fields","names","name","separated","types"));
    }
    
    public function displayCourtFields($name_id, $type_id)
    {
        $page_title = 'خانات المحكمة';
        $name = Name::where('id',$name_id)->first();
        $type = Type::where('id',$type_id)->first();
        $names = Name::lists('name','id');
        $types = Type::lists('type','id')->prepend("N/A",0);
        
        $court_fields = courtFields::where("court_name_id",$name_id)->where("court_type_id",$type_id)->lists("order","separated_id")->toArray();
        $separated = Separated::orderBy('title')->lists("title","id");
        
        return view('manage_court_fields/manage_court_fields', compact("page_title","court_fields","names","name","separated","types","type"));
    }
    
    public function store(courtFieldsRequest $request)
    {
        $data = [];
        
        $separated = $request->get("separated");
        $order = $request->get("order");
        
        courtFields::where('court_name_id', $request->get("id"))->where('court_type_id', $request->get("type_id"))->delete();
        if(is_array($separated))
            foreach ( $separated as $index => $separated )
                $data[] = ["separated_id" => $separated,"court_name_id" => $request->get("id"),"court_type_id" => $request->get("type_id"),"order" => $order[$separated][0]];
            
        courtFields::insert($data);
        
        return redirect("manage_court_fields/".(int)$request->get("id")."/".(int)$request->get("type_id"));
    }
}
