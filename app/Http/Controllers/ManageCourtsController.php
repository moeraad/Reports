<?php
namespace App\Http\Controllers;

use App\Court;
use App\Degree;
use App\District;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourtRequest;
use App\Name;
use App\Province;
use App\Speciality;
use App\Type;
use App\Zone;
use Illuminate\Support\Facades\Auth;

class ManageCourtsController extends Controller
{
    public function index()
    {
        $page_tilte = "عرض المحاكم";
//        if(Auth::user()->id == 1)
//            $courts = Court::where('province_id',5)->get();
//        else
            $courts = Court::all();
        
        $degrees = Degree::lists('degree', 'id');
        $provinces = Province::lists('title', 'id');
        $districts = District::lists('title', 'id');
        $zones = Zone::lists('title', 'id');
        $types = Type::lists('type', 'id');
        $names = Name::lists('name', 'id');
        $speciality = Speciality::lists('title', 'id');
        
        return view('manage_courts/list_courts', compact("page_title","courts","degrees","provinces", "districts", "zones","types","names","speciality"));
    }
    
    public function create()
    {
        $page_title = "محكمة جديدة";
        $degrees = Degree::lists('degree', 'id');
        $provinces = Province::lists('title', 'id');
        $districts = District::lists('title', 'id');
        $zones = Zone::lists('title', 'id');
        $types = Type::lists('type', 'id');
        $names = Name::lists('name', 'id');
        
        return view('manage_courts/manage_courts', compact("page_title","degrees","provinces", "districts", "zones","types","names"));
    }
    
    public function copy($id)
    {
        $page_title = "تعديل المحكمة";
        $court = Court::select('title','court_type_id','province_id','district_id','zone_id','court_degree_id','court_name_id','room')->where("id",$id)->first();
        $degrees = Degree::orderBy('degree')->lists('degree', 'id');
        $provinces = Province::orderBy('title')->lists('title', 'id');
        $districts = District::orderBy('title')->lists('title', 'id');
        $zones = Zone::orderBy('title')->lists('title', 'id');
        $types = Type::orderBy('type')->lists('type', 'id');
        $names = Name::orderBy('name')->lists('name', 'id');
        
        return view('manage_courts/manage_courts', compact("page_title","court","degrees","provinces", "districts", "zones","types","names"));
    }
    
    
    public function show($id)
    {
        $page_title = "تعديل المحكمة";
        $court = Court::where("id",$id)->first();
        $degrees = Degree::orderBy('degree')->lists('degree', 'id');
        $provinces = Province::orderBy('title')->lists('title', 'id');
        $districts = District::orderBy('title')->lists('title', 'id');
        $zones = Zone::orderBy('title')->lists('title', 'id');
        $types = Type::orderBy('type')->lists('type', 'id');
        $names = Name::orderBy('name')->lists('name', 'id');
        
        return view('manage_courts/manage_courts', compact("page_title","court","degrees","provinces", "districts", "zones","types","names"));
    }
    
    public function destroy($id)
    {
        Court::find($id)->delete();
        return redirect("manage_courts");
    }
    
    public function store(CourtRequest $request)
    {
        $court = new Court($request->all());
        $court->save();
        
        return redirect("manage_courts/" . $court->id);
    }
    
    public function update(CourtRequest $request)
    {
        $court_id = $request->get("id");
        $court = Court::findOrNew($court_id);
        $court->fill($request->all());
        $court->save();
        return redirect("manage_courts/" . $court->id);
    }
}