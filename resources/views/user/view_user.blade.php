@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>
        ملف المستخدم
    </h1>
</section>
<section class="content">
    <div class="box">
        <div class="pad">
            
        </div>
        <div class="box-body">
            {!! Form::open(['url' => 'user_profile','method' => 'post']) !!}
            <div class="row form-group">
                <div class="{{$errors->has('first_name')?' has-error':''}} col-lg-4">
                    <label>المناطق</label>
                    {!! Form::select('provinces[]', $provinces, explode(',',$user_profile->provinces), ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'tabindex' => "1",'multiple'=>'multiple']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="{{$errors->has('first_name')?' has-error':''}} col-lg-4">
                    <label>السنة</label>
                    {!! Form::select('current_year', [2016=>2016,2017=>2017,2018=>2018] , $user_profile->current_year, ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'tabindex' => "1"]) !!}
                </div>
            </div>
            <hr>
            <div>
                <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
@endsection