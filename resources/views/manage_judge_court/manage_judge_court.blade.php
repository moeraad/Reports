@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($judge_court->id))
        <a href="{{url("manage_judge_court/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        <a href="{{url("manage_judge_court/copy/".(isset($judge_court->id)? $judge_court->id : 0))}}" class="btn btn-app"><i class="fa fa-copy"></i>نسخ</a>
        {!! Form::open(['url' => 'manage_judge_court/'.$judge_court->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("manage_judge_court")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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
    {!! Form::open(['url' => isset($judge_court->id)?'manage_judge_court/'.$judge_court->id:'manage_judge_court','method' => isset($judge_court->id)?'put':'post']) !!}
        <div class="box-body">
            <input type='hidden' value='{{isset($judge_court->id)? $judge_court->id : 0}}' name='id'/>
            <div class="row form-group {{$errors->has('court_id') ? 'has-error' : ''}}">
                <div class="col-lg-2"></div>
                <label class="col-lg-1 text-left">المحكمة</label>
                <div class="col-lg-7">
                    {!! Form::select('court_id', $courts->toArray(), isset($judge_court->court_id)?$judge_court->court_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ','tabindex'=>'1']) !!}
                </div>
                @if(isset($judge_court->judge_id))
                <a href='{{url("manage_courts/".$judge_court->court_id)}}' class='btn btn-default'><i class='glyphicon glyphicon-link'></i></a>
                @endif
            </div>
            <div class="row form-group {{$errors->has('judge_id') ? 'has-error' : ''}}">
                <div class="col-lg-2"></div>
                <label class="col-lg-1 text-left">القاضي</label>
                <div class="col-lg-7">
                    {!! Form::select('judge_id', $judges->toArray(), isset($judge_court->judge_id)?$judge_court->judge_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ','tabindex'=>'2']) !!}
                </div>
                @if(isset($judge_court->judge_id))
                <a href='{{url("manage_judges/".$judge_court->judge_id)}}' class='btn btn-default'><i class='glyphicon glyphicon-link'></i></a>
                @endif
            </div>

            <div class="row form-group {{$errors->has('role_id') ? 'has-error' : ''}}">
                <div class="col-lg-2"></div>
                <label class="col-lg-1 text-left">الوظيفة</label>
                <div class="col-lg-7">
                    {!! Form::select('role_id', $roles->toArray(), isset($judge_court->role_id)?$judge_court->role_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ','tabindex'=>'3']) !!}
                </div>
            </div>

            <div class="row form-group">
                <div class="col-lg-2"></div>
                <label class="col-lg-1 text-left">المستشارين</label>
                <div class="col-lg-7">
                    <select class="form-control selectpicker show-tick show-menu-arrow" tabindex=4 multiple  data-live-search="true" data-size=5 name="advisors[]" title=" -- Select Option -- "  data-actions-box="true">
                        @foreach($judges as $id => $judge)
                        <option value="{{ $id }}" data-content="<div class='label label-success' style='display:inline-block'>{{ $judge }}</div>" {{ isset($advisors[$id])?"selected":"" }}>{{ $judge }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-lg-2"></div>
                <label class="col-lg-1 text-left">من تاريخ</label>
                <div class="col-lg-3">
                    {!! Form::text('date_from', isset($judge_court->date_from)?$judge_court->date_from:'', ['class' => 'form-control datepicker','tabindex'=>'5']) !!}
                </div>

                <label class="col-lg-1 text-left">إلى تاريخ</label>
                <div class="col-lg-3">
                    {!! Form::text('date_to', isset($judge_court->date_to)?$judge_court->date_to:'', ['class' => 'form-control datepicker','tabindex'=>'6']) !!}
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-default" tabindex=7><i class="fa fa-save"></i> حفظ</button>
            </div>
        </div>
    </form>
</div>
@endsection