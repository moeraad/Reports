@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($clerk_court->id))
        <a href="{{url("clerk_courts/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        {!! Form::open(['url' => 'clerk_courts/'.$clerk_court->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("clerk_courts")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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
    {!! Form::open(['url' => isset($clerk_court->id)?'clerk_courts/'.$clerk_court->id:'clerk_courts','method' => isset($clerk_court->id)?'put':'post']) !!}
        <div class="box-body">
            <input type='hidden' value='{{isset($clerk_court->id)?$clerk_court->id:0}}' name='id'/>
            <div class="row form-group">
                <div class="col-lg-3">
                    <label class="col-lg-1 text-left">المحكمة</label>
                    {!! Form::select('court_id', $courts, isset($clerk_court->court_id)?$clerk_court->court_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'data-style' => "btn-default"]) !!}
                </div>
                <div class="col-lg-9">
                    <label class="col-lg-1 text-left">الكتّاب</label>
                    {!! Form::select('clerk_id[]', $clerks, isset($court_clerks)?$court_clerks:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'data-style' => "btn-default", "multiple" => "true"]) !!}
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