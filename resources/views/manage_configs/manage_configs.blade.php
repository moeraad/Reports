@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($configs->id))
        <a href="{{url("manage_configs/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        {!! Form::open(['url' => 'manage_configs/'.$configs->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("manage_configs")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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
    {!! Form::open(['url' => isset($configs->id)?'manage_configs/'.$configs->id:'manage_configs','method' => isset($configs->id)?'put':'post']) !!}
        <div class="box-body">
            <input type='hidden' value='{{isset($configs->id)? $configs->id : 0}}' name='id'/>
            <div class="row form-group{{$errors->has('province_id')?' has-error':''}}">
                <label class="col-lg-4"></label>
                <label class="col-lg-1 text-left">المحافظة</label>
                <div class="col-lg-3">
                    {!! Form::select('province_id', $province, isset($configs->province_id)?$configs->province_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>
            </div>
            <div class="row form-group{{$errors->has('district_id')?' has-error':''}}">
                <label class="col-lg-4"></label>
                <label class="col-lg-1 text-left">القضاء</label>
                <div class="col-lg-3">
                    {!! Form::select('district_id', $district, isset($configs->district_id)?$configs->district_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>
            </div>
            <div class="row form-group{{$errors->has('zone_id')?' has-error':''}}">
                <label class="col-lg-4"></label>
                <label class="col-lg-1 text-left">المنطقة</label>
                <div class="col-lg-3">
                    {!! Form::select('zone_id', $zone, isset($configs->zone_id)?$configs->zone_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>
            </div>
            <div class="row form-group{{$errors->has('court_degree_id')?' has-error':''}}">
                <label class="col-lg-4"></label>
                <label class="col-lg-1 text-left">الدرجة</label>
                <div class="col-lg-3">
                    {!! Form::select('court_degree_id', $degree, isset($configs->court_degree_id)?$configs->court_degree_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>
            </div>
            <div class="row form-group{{$errors->has('role_id')?' has-error':''}}">
                <label class="col-lg-4"></label>
                <label class="col-lg-1 text-left">الوظيفة</label>
                <div class="col-lg-3">
                    {!! Form::select('role_id', $roles, isset($configs->role_id)?$configs->role_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ']) !!}
                </div>
            </div>
            <div class="row form-group">
                <label class="col-lg-4"></label>
                <label class="col-lg-1 text-left">العدد</label>
                <div class="col-lg-3">
                    {!! Form::text('count', isset($configs->count)?$configs->count:'', ['class' => 'form-control']) !!}
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