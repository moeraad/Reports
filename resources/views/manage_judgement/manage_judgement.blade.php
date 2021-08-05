@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($judgement->judge_id))
        <a href="{{url("manage_judgement/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        <a href="{{url("manage_judgement/duplicate") }}/{{$judgement->id}}" class="btn btn-app"><i class="fa fa-copy"></i>نسخ</a>
        {!! Form::open(['url' => 'manage_judgement/'.$judgement->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("manage_judgement")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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

    {!! Form::open(['url' => isset($judgement->id)?'manage_judgement/'.$judgement->id:'manage_judgement','method' => isset($judgement->id)?'put':'post']) !!}
    <form action="<?php echo url('manage_judgement/save'); ?>" method="post">
        <div class="box-body">
            <input type='hidden' value='{{isset($judgement->id)? $judgement->id : 0}}' name='id'/>
            <div class="row form-group">
                <div>
                    <div class="form-group{{$errors->has('judge_court_id')?' has-error':''}}">
                        <label class="col-lg-1 text-left">المحكمة</label>
                        <div class="col-lg-8">
                            <select tabindex="1" data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow" data-live-search="true" data-size=10 name="judge_court_id" title=" -- Select One -- " id="judgementJudgeCourt">
                                @foreach($judge_courts as $judge_court)
                                @if(isset($courts[$judge_court->court_id]) && isset($judges[$judge_court->judge_id]))
                                <option value="{{$judge_court->id}}" {{(isset($judgement->judge_court_id) && ($judgement->judge_court_id == $judge_court->id )) || (old('judge_court_id') == $judge_court->id)?"selected":""}}>{{$courts[$judge_court->court_id]}} | {{$judges[$judge_court->judge_id]}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="form-group{{$errors->has('report_date')?' has-error':''}}">
                        <label class="col-lg-1 text-left">التاريخ</label>
                        <div class="col-lg-2" id="dates_container">
                            {!! Form::select('report_date', $dates, isset($judgement->report_date)?$judgement->report_date:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'data-style' => "btn-default",'tabindex' => "2"]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='form-group{{$errors->has('judge_id')?' has-error':''}}'>
                    <label class="col-lg-1 text-left">القاضي</label>
                    <div class="col-lg-11" id="judges_container">
                        {!! Form::select('judge_id', $judges, isset($judgement->judge_id)?$judgement->judge_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default",'tabindex' => "3"]) !!}
                    </div>
                </div>
            </div>
            <hr>
            <div id="articles" class="{{ in_array('articles',$field_to_hide)?"hidden":"" }}">
                <div class="row">
                    <div class='form-group'>
                        <label class="col-lg-1 text-left">المواد</label>
                        <div class="col-lg-11">
                            <select tabindex="4" data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow" multiple  data-live-search="true" data-size=10 name="articles[]" title=" -- Select One -- "  data-actions-box="true">
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" data-content="<div class='label label-success' style='display:inline-block'>{{ $article->number }} {{ $article->name }}</div>" {{ isset($judgment_articles[$article->id]) || (is_array(old('articles')) && in_array($article->id,old('articles')))?"selected":"" }}>{{ $article->number }} | {{ $article->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <hr>
            </div>
            <div class="row">
                <div class="col-lg-4 form-group{{$errors->has('rule_number')?' has-error':''}}">
                    <label class="col-lg-4 text-left">الرقم</label>
                    <div class="col-lg-8">
                        {!! Form::text('rule_number', isset($judgement->rule_number)?$judgement->rule_number:'', ['class' => 'form-control','tabindex' => "5"]) !!}
                    </div>
                </div>

                <div class="col-lg-4 form-group{{$errors->has('decision_source')?' has-error':''}}{{ in_array('decision_source',$field_to_hide)?" hidden":"" }}" id="decision_source">
                    <label class="col-lg-4 text-left">مصدر القرار</label>
                    <div class="col-lg-8">
                        {!! Form::text('decision_source', isset($judgement->decision_source)?$judgement->decision_source:'', ['class' => 'form-control','tabindex' => "6"]) !!}
                    </div>
                </div>

                <div class="col-lg-4 form-group{{$errors->has('sessions_count')?' has-error':''}}">
                    <label class="col-lg-4 text-left">عدد الجلسات</label>
                    <div class="col-lg-8">
                        {!! Form::text('sessions_count', isset($judgement->sessions_count)?$judgement->sessions_count:'', ['class' => 'form-control','tabindex' => "7"]) !!}
                    </div>
                </div>
                
                <div class="col-lg-4 form-group{{$errors->has('speciality_id')?' has-error':''}}{{ in_array('speciality_id',$field_to_hide)?" hidden":"" }}" id="speciality_id">
                    <label class="col-lg-4 text-left">الإختصاص</label>
                    <div class="col-lg-8">
                        {!! Form::select('speciality_id', $specialities->toArray(), isset($judgement->speciality_id)?$judgement->speciality_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default",'tabindex' => "8"]) !!}
                    </div>
                </div>

                <div class="col-lg-4 form-group{{$errors->has('status_id')?' has-error':''}}{{ in_array('status_id',$field_to_hide)?" hidden":"" }}" id="status_id">
                    <label class="col-lg-4 text-left">طبيعة الحكم</label>
                    <div class="col-lg-8">
                        {!! Form::select('status_id', $statuses->toArray(), isset($judgement->status_id)?$judgement->status_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default",'tabindex' => "9"]) !!}
                    </div>
                </div>


                <div class="col-lg-4 form-group{{$errors->has('judgment_type_id')?' has-error':''}}{{ in_array('judgment_type_id',$field_to_hide)?" hidden":"" }}" id="judgment_type_id">
                    <label class="col-lg-4 text-left">نتيجة الحكم</label>
                    <div class="col-lg-8">
                        {!! Form::select('judgment_type_id', $judgement_types, isset($judgement->judgment_type_id)?$judgement->judgment_type_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 5, "title" => ' -- Select One -- ', 'data-style' => "btn-default",'tabindex' => "10"]) !!}
                    </div>
                </div>
                
                <div class="col-lg-4 form-group{{$errors->has('arrival_date')?' has-error':''}}">
                    <label class="col-lg-4 text-left">تاريخ الورود</label>
                    <div class="col-lg-8">
                        {!! Form::text('arrival_date', isset($judgement->arrival_date)?$judgement->arrival_date:'', ['class' => 'form-control datepicker text-right','autocomplete' => 'off','tabindex' => "11"]) !!}
                    </div>
                </div>

                <div class="col-lg-4 form-group{{$errors->has('last_session')?' has-error':''}}">
                    <label class="col-lg-4 text-left">الجلسة الختامية</label>
                    <div class="col-lg-8">
                        {!! Form::text('last_session', isset($judgement->last_session)&&$judgement->last_session>0?$judgement->last_session:'', ['class' => 'form-control datepicker text-right','autocomplete' => 'off','tabindex' => "12"]) !!}
                    </div>
                </div>

                <div class="col-lg-4 form-group{{$errors->has('judgement_date')?' has-error':''}}">
                    <label class="col-lg-4 text-left">تاريخ الحكم</label>
                    <div class="col-lg-8">
                        {!! Form::text('judgement_date', isset($judgement->judgement_date)?$judgement->judgement_date:'', ['class' => 'form-control datepicker text-right','autocomplete' => 'off','tabindex' => "13"]) !!}
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class='form-group{{$errors->has('notes')?' has-error':''}}'>
                    <label class="col-lg-1 text-left">ملاحظات</label>
                    <div class="col-lg-11">
                        {!! Form::textArea('notes', isset($judgement->notes)?$judgement->notes:'', ['class' => 'form-control', 'rows' => "3",'tabindex' => "14"]) !!}
                    </div>
                </div>
            </div>
            <hr>
            <div>
                <button type="submit" tabindex="15" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
            </div>
        </div>
    </form>
</div>

@endsection