@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($judge_clerk->id))
        <a href="{{url("judge_clerks/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        {!! Form::open(['url' => 'judge_clerks/'.$judge_clerk->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("judge_clerks")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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
    {!! Form::open(['url' => isset($judge_clerk->id)?'judge_clerks/'.$judge_clerk->id:'judge_clerks','method' => isset($judge_clerk->id)?'put':'post']) !!}
        <div class="box-body">
            <input type='hidden' value='{{isset($judge_clerk->id)?$judge_clerk->id:0}}' name='id'/>
            <div class="row form-group">
                <div class="col-lg-3">
                    <label class="col-lg-1 text-left">الإسم</label>
                    {!! Form::select('clerk_id', $clerks, isset($judge_clerk->clerk_id)?$judge_clerk->clerk_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'data-style' => "btn-default"]) !!}
                </div>
                <div class="col-lg-3">
                    <label class="col-lg-1 text-left">القاضي</label>
                    {!! Form::select('judge_id', $judges, isset($judge_clerk->judge_id)?$judge_clerk->judge_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'data-style' => "btn-default"]) !!}
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