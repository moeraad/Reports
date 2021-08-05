@extends('layouts.app')

@section('content')
<style>
    .width_12{width:12%;display: inline-block;}
</style>
<div class="box">
    @if($errors->has())
    <div class="box-body">
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            @foreach ($errors->all() as $error)
            <div>{!! $error !!}</div>
            @endforeach
        </div>
    </div>
    @endif
    {!! Form::open(['url' => 'swap','get']) !!}
    <div class="box-body" style="font-size: 12px;">
        <div class='row form-group'>
            <label class="col-lg-1 text-left">من محكمة</label>
            <div class="col-lg-8{{$errors->has('from')?' has-error':''}}">
                <select tabindex="1" class="form-control selectpicker show-tick show-menu-arrow" title=' -- Select One -- ' 
                        data-style="btn-default" data-live-search="true" data-size=10 name="from">
                    @foreach($judge_courts as $judge_court)
                    @if(isset($courts[$judge_court->court_id]) && isset($judges[$judge_court->judge_id]))
                    <option value="{{$judge_court->id}}" {{(isset($from) && $from == $judge_court->id) || (old('judge_court_id') == $judge_court->id)?"selected":""}}>
                        {{$courts[$judge_court->court_id] . ' | ' . $judges[$judge_court->judge_id]}}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            <label class="col-lg-1 text-left">بتاريخ</label>
            <div class="col-lg-2{{$errors->has('from_date')?' has-error':''}}">
                <input type="text" name="from_date" value="{{isset($from_date)?$from_date:''}}" class="form-control datepicker text-right"/>
            </div>
        </div>
        <div class='row form-group'>
            <label class="col-lg-1 text-left">إلى محكمة</label>
            <div class="col-lg-8{{$errors->has('to')?' has-error':''}}">
                <select tabindex="1" class="form-control selectpicker show-tick show-menu-arrow" title=' -- Select One -- ' 
                        data-style="btn-default" data-live-search="true" data-size=10 name="to">
                    @foreach($judge_courts as $judge_court)
                    @if(isset($courts[$judge_court->court_id]) && isset($judges[$judge_court->judge_id]))
                    <option value="{{$judge_court->id}}" 
                            {{(isset($to) && $to == $judge_court->id) || (old('judge_court_id') == $judge_court->id)?"selected":""}}>
                        {{$courts[$judge_court->court_id] . ' | ' . $judges[$judge_court->judge_id]}}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            <label class="col-lg-1 text-left">بتاريخ</label>
            <div class="col-lg-2{{$errors->has('to_date')?' has-error':''}}">
                <input type="text" name="to_date" value="{{isset($to_date)?$to_date:''}}" class="form-control datepicker text-right"/>
            </div>
        </div>
        <hr>
        <div>
            <button type="submit" tabindex="30" class="btn btn-default"> مبادلة <i class="fa fa-arrow-left"></i></button>
        </div>
        </form>
    </div>
    @endsection