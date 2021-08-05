@extends('layouts.app')

@section('content')
<div class="box">
    <div class="pad">
        @if(isset($judge->id))
        <a href="{{url("profile/".$judge->id)}}" class="btn btn-app"><i class="fa fa-user"></i>عرض</a>
        <a href="{{url("manage_judges/create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        {!! Form::open(['url' => 'manage_judges/'.$judge->id, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("manage_judges")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
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
    {!! Form::open(['url' => isset($judge->id)?'manage_judges/'.$judge->id:'manage_judges','method' => isset($judge->id)?'put':'post', 'files'=>true]) !!}

    <div class="box-body">
        <div class="col-lg-6">
            <input type='hidden' value='{{isset($judge->id)? $judge->id : 0}}' name='id'/>
            <input type='hidden' value='0' name='remove_photo'/>
            <div class="row form-group{{$errors->has('name')?' has-error':''}}">
                <div class='col-lg-12'>
                    <label class="col-lg-1 text-left">صورة</label>
                    {!! Form::file('photo', ['class' => 'form-control']) !!}
                    @if(isset($judge->photo) && $judge->photo)
                    <div class="pull-right col-lg-2" style='margin-top: -90px;'>
                        <div class='text-center' style="display: inline-block">
                            <img class="profile-user-img img-responsive img-circle" src="{{url($judge->photo)}}" alt="User profile picture" width="128">
                            <a href='#' id='remove_photo'>remove photo</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row form-group">
                <div class="{{$errors->has('first_name')?' has-error':''}} col-lg-4">
                    <label>الإسم</label>
                    <input type="text" class="form-control" name="first_name" value="{{isset($judge->first_name)?$judge->first_name:''}}"/>
                </div>
                <div class="{{$errors->has('middle_name')?' has-error':''}} col-lg-4">
                    <label>إسم الأب</label>
                    <input type="text" class="form-control" name="middle_name" value="{{isset($judge->middle_name)?$judge->middle_name:''}}"/>
                </div>
                
                <div class="{{$errors->has('last_name')?' has-error':''}} col-lg-4">
                    <label>الكنية</label>
                    <input type="text" class="form-control" name="last_name" value="{{isset($judge->last_name)?$judge->last_name:''}}"/>
                </div>
            </div>
            
            <div class="row form-group">
                <div class="{{$errors->has('mother_name')?' has-error':''}} col-lg-4">
                    <label>إسم الأم</label>
                    <input type="text" class="form-control" name="mother_name" value="{{isset($judge->mother_name)?$judge->mother_name:''}}"/>
                </div>
                <div class="{{$errors->has('register_number')?' has-error':''}} col-lg-4">
                    <label>رقم السجل</label>
                    <input type="text" class="form-control" name="register_number" value="{{isset($judge->register_number)?$judge->register_number:''}}"/>
                </div>
                <div class="col-lg-4">
                    <label>حساب فاعل</label>
                    <div>
                        {!! Form::radio('active', '1', isset($judge->active)&&$judge->active == 1) !!} فاعل
                        {!! Form::radio('active', '0', isset($judge->active)&&$judge->active == 0) !!} غير فاعل
                    </div>
                </div>
            </div>
            
            
            <hr>
            <div class="row form-group">
                <div class="col-lg-4">
                    <label>تاريخ الولادة</label>
                    {!! Form::text('birth_date', isset($judge->birth_date)?$judge->birth_date:'', ['class' => 'form-control datepicker text-right']) !!}
                </div>

                <div class="col-lg-4">
                    <label>مكان الولادة</label>
                    {!! Form::text('birth_place', isset($judge->birth_place)?$judge->birth_place:'', ['class' => 'form-control']) !!}
                </div>

                <div class="col-lg-4">
                    <label>الجنس</label>
                    <div>
                        {!! Form::radio('sex', 'm', isset($judge->sex)&&$judge->sex == "m") !!} ذكر
                        {!! Form::radio('sex', 'f', isset($judge->sex)&&$judge->sex == "f") !!} أنثى
                    </div>
                </div>
            </div>
            <hr>
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>هاتف</label>
                    {!! Form::text('phone', isset($judge->phone)?$judge->phone:'', ['class' => 'form-control']) !!}
                </div>

                <div class="col-lg-6">
                    <label>منزل</label>
                    {!! Form::text('phone2', isset($judge->phone2)?$judge->phone2:'', ['class' => 'form-control']) !!}
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>مكتب</label>
                    {!! Form::text('office', isset($judge->office)?$judge->office:'', ['class' => 'form-control']) !!}
                </div>

                <div class="col-lg-6">
                    <label>خليوي</label>
                    {!! Form::text('mobile', isset($judge->mobile)?$judge->mobile:'', ['class' => 'form-control']) !!}
                </div>
            </div>
                
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>بريد إلكتروني</label>
                    {!! Form::text('email', isset($judge->email)?$judge->email:'', ['class' => 'form-control']) !!}
                </div>
            </div>
            
            <hr>
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>نوع السيارة</label>
                    {!! Form::text('car_type', isset($judge->car_type)?$judge->car_type:'', ['class' => 'form-control']) !!}
                </div>
                <div class="col-lg-6">
                    <label>رقم السيارة</label>
                    {!! Form::text('car_num', isset($judge->car_num)?$judge->car_num:'', ['class' => 'form-control']) !!}
                </div>
            </div>
            <hr>
            <div class="row form-group">
                <div class="col-lg-12">
                    <label>العنوان</label>
                    {!! Form::text('residence', isset($judge->residence)?$judge->residence:'', ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>محافظة</label>
                    {!! Form::select('province_id', $provinces, isset($judge->province_id)?$judge->province_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'tabindex' => "2"]) !!}
                </div>

                <div class="col-lg-6">
                    <label>قضاء</label>
                    {!! Form::select('district_id', $districts, isset($judge->district_id)?$judge->district_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'tabindex' => "2"]) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>المنطقة</label>
                    {!! Form::select('zone_id', $zones, isset($judge->zone_id)?$judge->zone_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ",'tabindex' => "2"]) !!}
                </div>

                <div class="col-lg-6">
                    <label>الطائفة</label>
                    {!! Form::select('sect', $sects, isset($judge->sect)?$judge->sect:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ",'tabindex' => "2"]) !!}
                </div>
            </div>
            <hr>
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>دخول الخدمة</label>
                    {!! Form::text('date_service', isset($judge->date_service)?$judge->date_service:'', ['class' => 'form-control datepicker text-right']) !!}
                </div>
                <div class="col-lg-6">
                    <label>دخول القضاء</label>
                    {!! Form::text('date_juridation', isset($judge->date_juridation)?$judge->date_juridation:'', ['class' => 'form-control datepicker text-right']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-lg-6">
                    <label>تاريخ الترقية</label>
                    {!! Form::text('date_promotion', isset($judge->date_promotion)?$judge->date_promotion:'', ['class' => 'form-control datepicker text-right']) !!}
                </div>
                <div class="col-lg-6">
                    <label>الدرجة</label>
                    {!! Form::text('degree', isset($judge->degree)?$judge->degree:'', ['class' => 'form-control']) !!}
                </div>
            </div>
            <hr>
            <div>
                <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
            </div>
        </div>
    </div>
</form>
</div>
@endsection