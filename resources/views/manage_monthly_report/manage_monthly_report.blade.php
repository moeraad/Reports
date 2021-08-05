@extends('layouts.app')

@section('content')
<style>
    .width_12{width:12%;display: inline-block;}
</style>
<div class="box">
    <div class="pad">
        @if(isset($monthly_report->id))
        <a href="{{url("monthly_reports/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        <a href="{{url("manage_monthly_report/duplicate") }}/{{$monthly_report->id}}" class="btn btn-app"><i class="fa fa-copy"></i>نسخ</a>
        {!! Form::open(['url' => 'monthly_reports/'.$monthly_report->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("monthly_reports")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
    </div>
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
    {!! Form::open(['url' => isset($monthly_report->id)?'monthly_reports/'.$monthly_report->id:'monthly_reports','method' => isset($monthly_report->id)?'put':'post']) !!}
    <div class="box-body" style="font-size: 12px;">
        <input type='hidden' value='{{isset($monthly_report->id)? $monthly_report->id : 0}}' name='id'/>
        <div class="row">
            <div class='form-group{{$errors->has('judge_court_id')?' has-error':''}}'>
                <label class="col-lg-1 text-left">المحكمة</label>
                <div class="col-lg-11">
                    <select tabindex="1" class="form-control selectpicker show-tick show-menu-arrow" title=' -- Select One -- ' data-style="btn-default" data-live-search="true" data-size=10 name="judge_court_id" id='monthlyReportJudgeCourt'>
                        @foreach($judge_courts as $judge_court)
                        @if(isset($courts[$judge_court->court_id]) && isset($judges[$judge_court->judge_id]))
                        <option value="{{$judge_court->id}}" {{(isset($monthly_report->judge_court_id) && ($monthly_report->judge_court_id==$judge_court->id )) || (old('judge_court_id') == $judge_court->id)?"selected":""}}>
                            {{$courts[$judge_court->court_id] . ' | ' . $judges[$judge_court->judge_id]}}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div>
                <div class="form-group{{$errors->has('judge_id')?' has-error':''}}">
                    <label class="col-lg-1 text-left">القاضي</label>
                    <div class="col-lg-7">
                        {!! Form::select('judge_id', $judges, isset($monthly_report->judge_id)?$monthly_report->judge_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default",'tabindex' => "2", 'id' => 'judgesDropdown']) !!}
                    </div> 
                </div>
            </div>
            <div>
                <div class="form-group{{$errors->has('speciality_id')?' has-error':''}}">
                    <label class="col-lg-1 text-left">الإختصاص</label>
                    <div class="col-lg-3">
                        {!! Form::select('speciality_id', $specialities, isset($monthly_report->speciality_id)?$monthly_report->speciality_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default",'tabindex' => "3"]) !!}
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-12" id="fields_container">
                <div id="rotated" class="form-group width_12{{$errors->has('rotated')?' has-error':''}}{{ in_array('rotated',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">مدوّر</label>
                    <div>
                        {!! Form::text('rotated', isset($monthly_report->rotated)?$monthly_report->rotated:0, ['class' => 'form-control','tabindex' => "4"]) !!}
                    </div>
                </div>
                <div id="pretencesArrival" class="form-group width_12{{$errors->has('pretencesArrival')?' has-error':''}}{{ in_array('pretencesArrival',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">وارد إدعاء نيابة</label>
                    <div>
                        {!! Form::text('pretencesArrival', isset($monthly_report->pretencesArrival)?$monthly_report->pretencesArrival:0, ['class' => 'form-control','tabindex' => "5"]) !!}
                    </div>
                </div>
                <div id="arrivalDirectComplaint" class="form-group width_12{{$errors->has('arrivalDirectComplaint')?' has-error':''}}{{ in_array('arrivalDirectComplaint',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">وارد شكوى مباشرة</label>
                    <div>
                        {!! Form::text('arrivalDirectComplaint', isset($monthly_report->arrivalDirectComplaint)?$monthly_report->arrivalDirectComplaint:0, ['class' => 'form-control','tabindex' => "6"]) !!}
                    </div>
                </div>
                <div id="arriving" class="form-group width_12{{$errors->has('arriving')?' has-error':''}}{{ in_array('arriving',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">الوارد</label>
                    <div>
                        {!! Form::text('arriving', isset($monthly_report->arriving)?$monthly_report->arriving:0, ['class' => 'form-control','tabindex' => "7"]) !!}
                    </div>
                </div>
                <div id="eliminatedArrival" class="form-group width_12{{$errors->has('eliminatedArrival')?' has-error':''}}{{ in_array('eliminatedArrival',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">الوارد المشطوب</label>
                    <div>
                        {!! Form::text('eliminatedArrival', isset($monthly_report->eliminatedArrival)?$monthly_report->eliminatedArrival:0, ['class' => 'form-control','tabindex' => "8"]) !!}
                    </div>
                </div>
                <div id="totalCases" class="form-group width_12{{$errors->has('totalCases')?' has-error':''}}{{ in_array('totalCases',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">المجموع العام</label>
                    <div>
                        {!! Form::text('totalCases', isset($monthly_report->totalCases)?$monthly_report->totalCases:0, ['class' => 'form-control','tabindex' => "9"]) !!}
                    </div>
                </div>
                <div id="casesOnSchedule" class="form-group width_12{{$errors->has('casesOnSchedule')?' has-error':''}}{{ in_array('casesOnSchedule',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">على الجدول</label>
                    <div>
                        {!! Form::text('casesOnSchedule', isset($monthly_report->casesOnSchedule)?$monthly_report->casesOnSchedule:0, ['class' => 'form-control','tabindex' => "10"]) !!}
                    </div>
                </div>
                <div id="totalSeparated" class="form-group width_12{{$errors->has('totalSeparated')?' has-error':''}}{{ in_array('totalSeparated',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">مجموع المفصول</label>
                    <div>
                        {!! Form::text('totalSeparated', isset($monthly_report->totalSeparated)?$monthly_report->totalSeparated:0, ['class' => 'form-control','tabindex' => "11"]) !!}
                    </div>
                </div>
                <div id="remainedCases" class="form-group width_12{{$errors->has('remainedCases')?' has-error':''}}{{ in_array('remainedCases',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">الباقي</label>
                    <div>
                        {!! Form::text('remainedCases', isset($monthly_report->remainedCases)?$monthly_report->remainedCases:0, ['class' => 'form-control','tabindex' => "12"]) !!}
                    </div>
                </div>
                <div id="forExecution" class="form-group width_12{{$errors->has('forExecution')?' has-error':''}}{{ in_array('forExecution',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">محالة للتنفيذ</label>
                    <div>
                        {!! Form::text('forExecution', isset($monthly_report->forExecution)?$monthly_report->forExecution:0, ['class' => 'form-control','tabindex' => "13"]) !!}
                    </div>
                </div>
                <div id="executed" class="form-group width_12{{$errors->has('executed')?' has-error':''}}{{ in_array('executed',$field_to_hide)?" hidden":"" }}">
                    <label class="text-nowrap">منفّذة</label>
                    <div>
                        {!! Form::text('executed', isset($monthly_report->executed)?$monthly_report->executed:0, ['class' => 'form-control','tabindex' => "14"]) !!}
                    </div>
                </div>
                <div class="form-group width_12{{$errors->has('year')?' has-error':''}}">
                    <label class="text-nowrap">السنة</label>
                    <div>
                        {!! Form::select('year',years(), isset($monthly_report->year)?$monthly_report->year:date('Y'), ['class' => 'form-control selectpicker show-tick show-menu-arrow', 'data-style' => "btn-default",'tabindex' => "16"]) !!}
                    </div>
                </div>
                <div class="form-group width_12{{$errors->has('month')?' has-error':''}}">
                    <label class="text-nowrap">الشهر</label>
                    <div>
                        {!! Form::select('month',months(), isset($monthly_report->month)?$monthly_report->month:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow', 'data-style' => "btn-default",'data-live-search' => "true", "title" => ' -- Select One -- ','tabindex' => "17"]) !!}
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div>
            <div class="row form-group">
                <div class="col-lg-12" id='fieldsContainer'>
                <?php
                $tabIndex = 18;
                if (isset($fields))
                {
                    foreach ($fields as $field)
                    {
                        if( isset($separated[$field->separated_id]) )
                        {
                            ?>
                            <div class="width_12">
                                <label class="text-nowrap">{{$separated[$field->separated_id]}}</label>
                                <div>
                                    {!! Form::text('separated['.$field->separated_id.']', isset($separated_reports[$field->separated_id])?$separated_reports[$field->separated_id]:0, ['class' => 'form-control','tabindex' => $tabIndex]) !!}
                                </div>
                            </div>
                            <?php
                            $tabIndex++;   
                        }
                    }
                }
                ?>
                </div>
            </div>
        </div>
<hr>
<div>
    <button type="submit" tabindex="30" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
</div>
</form>
</div>
@if( null !== old('_token') )
<script>
    /*$(function(){
        $("#monthlyReportJudgeCourt").trigger('change');
    })*/
</script>
@endif
@endsection