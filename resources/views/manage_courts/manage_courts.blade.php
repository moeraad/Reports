@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($court->id))
        <a href="{{url("manage_courts/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        <a href="{{url("manage_courts/copy/".(isset($court->id)? $court->id : 0))}}" class="btn btn-app"><i class="fa fa-copy"></i>نسخ</a>
        {!! Form::open(['url' => 'manage_courts/'.$court->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("manage_courts")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
    </div>
    @if($errors->has())
    <div class="box-body">
        <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    </div>
    @endif
    {!! Form::open(['url' => isset($court->id)?'manage_courts/'.$court->id:'manage_courts','method' => isset($court->id)?'put':'post']) !!}
        <div class="box-body">
            <input type='hidden' value='{{isset($court->id)? $court->id : 0}}' name='id'/>
            <div class="row form-group {{$errors->has('title') ? 'has-error' : ''}}">
                <label class="col-lg-1 text-left">الإسم</label>
                <div class="col-lg-11">
                    {!! Form::text('title', isset($court->title)?$court->title:'', ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="row form-group {{$errors->has('court_name_id') ? 'has-error' : ''}}">
                <label class="col-lg-1 text-left">الفئة</label>
                <div class="col-lg-7">
                    {!! Form::select('court_name_id', [0=>'N/A'] + $names->toArray(), isset($court->court_name_id)?$court->court_name_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>

            </div>

            <div class="row form-group">
                <div class=' {{$errors->has('court_type_id') ? 'has-error' : ''}}'>
                    <label class="col-lg-1 text-left">النوع</label>
                    <div class="col-lg-3">
                        {!! Form::select('court_type_id', [0=>'N/A'] + $types->toArray(), isset($court->court_type_id)?$court->court_type_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-size' => 10, "title" => ' -- Select One -- ']) !!}
                    </div>
                </div>

                <div class='{{$errors->has('court_degree_id') ? 'has-error' : ''}}'>
                    <label class="col-lg-1 text-left">الدرجة</label>
                    <div class="col-lg-3">
                        {!! Form::select('court_degree_id', [0=>'N/A'] + $degrees->toArray(), isset($court->court_degree_id)?$court->court_degree_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-size' => 10, "title" => ' -- Select One -- ']) !!}
                    </div>
                </div>

                <label class="col-lg-1 text-left">الغرفة</label>
                <div class="col-lg-3">
                    {!! Form::text('room', isset($court->room)?$court->room:'', ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="row form-group">
                <div class='{{$errors->has('province_id') ? 'has-error' : ''}}'>
                    <label class="col-lg-1 text-left">المحافظة</label>
                    <div class="col-lg-3">
                        {!! Form::select('province_id', [0=>'N/A'] + $provinces->toArray(), isset($court->province_id)?$court->province_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-size' => 10, "title" => ' -- Select One -- ']) !!}
                    </div>
                </div>
                <div class='{{$errors->has('district_id') ? 'has-error' : ''}}'>
                    <label class="col-lg-1 text-left">القضاء</label>
                    <div class="col-lg-3">
                        {!! Form::select('district_id', [0=>'N/A'] + $districts->toArray(), isset($court->district_id)?$court->district_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                    </div>
                </div>
                <label class="col-lg-1 text-left">المنطقة</label>
                <div class="col-lg-3">
                    {!! Form::select('zone_id', [0=>'N/A'] + $zones->toArray(), isset($court->zone_id)?$court->zone_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>
            </div>
            <hr>
            <div>
                <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
            </div>
        </div>
    </form>
</div>
@endsection