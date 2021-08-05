@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($clerk->id))
        <a href="{{url("clerk/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        {!! Form::open(['url' => 'clerk/'.$clerk->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("clerk")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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
    {!! Form::open(['url' => isset($clerk->id)?'clerk/'.$clerk->id:'clerk','method' => isset($clerk->id)?'put':'post']) !!}
        <div class="box-body">
            <input type='hidden' value='{{isset($clerk->id)?$clerk->id:0}}' name='id'/>
            <div class="row form-group">
                <div class="col-lg-2">
                    <label class="col-lg-1 text-left">الإسم</label>
                    <input class="form-control" name="first_name" type="text" value="{{isset($clerk->first_name)?$clerk->first_name:''}}">
                </div>
                <div class="col-lg-2">
                    <label class="col-lg-1 text-left">الكنية</label>
                    <input class="form-control" name="last_name" type="text" value="{{isset($clerk->last_name)?$clerk->last_name:''}}">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                    <label>الجنس</label>
                    <div>
                        {!! Form::radio('sex', 'm', isset($clerk->sex)&&$clerk->sex == "m") !!} ذكر
                        {!! Form::radio('sex', 'f', isset($clerk->sex)&&$clerk->sex == "f") !!} أنثى
                    </div>
                </div>
            </div>
            <hr>
            <div>
                <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection