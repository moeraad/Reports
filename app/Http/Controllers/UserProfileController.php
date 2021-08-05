<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Province;
use App\userProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class UserProfileController extends Controller
{
    function index()    
    {
        $provinces = Province::lists("title","id");
        $user_profile = userProfile::where("user_id", Auth::user()->id)->first();
        
        return view("user/view_user", compact('provinces','user_profile'));
    }
    
    function store()
    {
        $provinces = implode(",", Input::get("provinces"));
        $current_year = Input::get("current_year");
        
        $user_profile = userProfile::where("user_id", Auth::user()->id)->first();
        $user_profile->user_id = Auth::user()->id;
        $user_profile->provinces = $provinces;
        $user_profile->current_year = $current_year;
        $user_profile->save();
        Session::set('provinces', $provinces);
        Session::set('current_year', $current_year);

        return redirect("user_profile");
    }
}
