<?php

namespace App\Http\Controllers;

use App\Configs;
use App\Degree;
use App\District;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\configRequest;
use App\Province;
use App\Role;
use App\Zone;

class ConfigsController extends Controller
{
    public function index()
    {
        $configs = Configs::all();
        $page_title = "التشكيلات القضائية";
        return view('manage_configs/list_configs', compact("page_title","configs"));
    }
    
    public function show($id = 0)
    {
        $configs = Configs::where("id",$id)->first();
        $province = Province::lists("title","id");
        $district = District::lists("title","id");
        $zone = Zone::lists("title","id");
        $degree = Degree::lists("degree","id");
        $roles = Role::lists("title","id");
        
        return view('manage_configs/manage_configs', ["page_title" => "التشكيلات القضائية","configs" => $configs,"province" => $province, "district" => $district, "zone" => $zone, "degree" => $degree,"roles" => $roles]);
    }
    
    public function create()
    {
        $page_title = "التشكيلات القضائية";
        $province = Province::lists("title","id");
        $district = District::lists("title","id");
        $zone = Zone::lists("title","id");
        $degree = Degree::lists("degree","id");
        $roles = Role::lists("title","id");
        
        return view('manage_configs/manage_configs', compact('page_title','province','district','zone','degree','roles'));
    }
    
    public function destroy($id)
    {
        Configs::find($id)->delete();
        return redirect("manage_configs");
    }
    
    public function store(configRequest $request)
    {
        $config = new Configs($request->all());
        $config->save();
        
        return redirect("manage_configs/" . $config->id);
    }
    
    public function update(configRequest $request)
    {
        $config = Configs::findOrNew($request->get("id"));
        $config->fill($request->all())->save();
        
        return redirect("manage_configs/" . $config->id);
    }
}
