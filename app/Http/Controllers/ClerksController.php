<?php

namespace App\Http\Controllers;

use App\Clerk;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClerkRequest;

class ClerksController extends Controller
{
    function index()
    {
        $clerks = Clerk::all();
         return view("clerk/list_clerks", compact("clerks"));
    }
    
    function show($id)
    {
        $clerk = Clerk::where("id", $id)->first();
        
        return view("clerk/manage_clerk", compact("clerk"));
    }
    
    function create()
    {
        return view("clerk/manage_clerk");
    }
    
    function store(ClerkRequest $request)
    {
        $clerk = new Clerk($request->all());
        $clerk->save();
        
        return view("clerk/manage_clerk");
    }
    
    function update(ClerkRequest $request)
    {
        $clerk = Clerk::findOrNew($request->get("id"));
        $clerk->fill($request->all());
        $clerk->save();
        
        return redirect("clerk/".$clerk->id);
    }
    
    function destroy($id)
    {
        Clerk::find($id)->delete();
        return redirect("clerk");
    }
}
