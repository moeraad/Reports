@extends('layouts.app')
@section('content')
<div class="box collapsed-box">
    <div class="box-header ui-sortable-handle" style="cursor: move;">
        <i class="fa fa-calendar"></i>
        <h3 class="box-title">بحث متقدم</h3>
        <!-- tools box -->
        <div class="pull-left box-tools">
            <!-- button with a dropdown -->
            <button class="btn btn-danger btn-sm" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div><!-- /. tools -->
    </div><!-- /.box-header -->
    <div class="box-body no-padding">
        <div class="col-lg-12">
            {!! Form::open(['url' => 'manage_judges/filter','method' => 'POST']) !!}
            <div>
                <div class="row form-group">
                    <div class="col-lg-3">
                        <label>الجنس</label>
                        <div>
                            {!! Form::radio('sex', 'm', "") !!} ذكر
                            {!! Form::radio('sex', 'f', "") !!} أنثى
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>الخانات</label>
                        <select name="filter_fields[]" class="form-control selectpicker show-tick show-menu-arrow" data-live-search="true" data-size=15 title=" -- Select One -- " tabindex="2" multiple data-actions-box="true">
                            @foreach($all_fields as $field_name => $field_title)
                            <option title="{{$field_name}}" value="{{$field_name}}" {{isset($fields[$field_name])?"selected":""}}>{{$field_title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>متقاعد</label>
                        <div>
                            {!! Form::radio('retired', '1', "") !!} نعم
                            {!! Form::radio('retired', '0', "1") !!} لا
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>حساب فاعل</label>
                        <div>
                            {!! Form::radio('active', '1', "1") !!} نعم
                            {!! Form::radio('active', '0', "") !!} لا
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-lg-3">
                        <label>محافظة</label>
                        {!! Form::select('province_id', $provinces, old('province_id'), ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'tabindex' => "2"]) !!}
                    </div>

                    <div class="col-lg-3">
                        <label>قضاء</label>
                        {!! Form::select('district_id', $districts, old('district_id'), ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'tabindex' => "2"]) !!}
                    </div>

                    <div class="col-lg-3">
                        <label>المنطقة</label>
                        {!! Form::select('zone_id', $zones, old('zone_id'), ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ",'tabindex' => "2"]) !!}
                    </div>

                    <div class="col-lg-3">
                        <label>الطائفة</label>
                        {!! Form::select('sect', $sects, old('sect'), ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ",'tabindex' => "2"]) !!}
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-lg-3">
                        <label>دخول الخدمة</label>
                        {!! Form::text('date_service', old('date_service'), ['class' => 'form-control text-right']) !!}
                    </div>
                    <div class="col-lg-3">
                        <label>دخول القضاء</label>
                        {!! Form::text('date_juridation', old('date_juridation'), ['class' => 'form-control text-right']) !!}
                    </div>
                    <div class="col-lg-3">
                        <label>تاريخ الترقية</label>
                        {!! Form::text('date_promotion', old('date_promotion'), ['class' => 'form-control text-right']) !!}
                    </div>
                    <div class="col-lg-3">
                        <label>الدرجة</label>
                        <input type="text" id='degree' name='degree' data-slider-id='slider_zone'/>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> بحث</button>
            <a class="btn btn-default" href="{{url('manage_judges')}}"><i class="fa fa-undo"></i>رجوع </a>
            </form>
            <br/>
        </div>
    </div><!-- /.box-body -->
</div>

<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th>ID</th>
                    <th>الإسم</th>
                    @foreach($fields as $field_name => $field_title)
                    <th>{{$field_title}}</th>
                    @endforeach
                    <th width="10"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($judges as $judge)
                {
                    ?>
                    <tr>
                        <td>{{$judge->id}}</td>
                        <td><a href="{{url('profile',$judge->id)}}">{{$judge->first_name}} {{$judge->middle_name}} {{$judge->last_name}}</a></td>
                        @foreach($fields as $field_name => $field_title)
                        @if($field_name=="father_name")
                        <td>{{$judge->middle_name}}</td>
                        @endif
                        @if($field_name=="mother_name")
                        <td>{{$judge->mother_name}}</td>
                        @endif
                        @if($field_name=="sect")
                        <td>{{$judge->sect}}</td>
                        @endif
                        @if($field_name=="sex")
                        <td>{{$judge->sex=="m"?"ذكر":"أنثى"}}</td>
                        @endif
                        @if($field_name=="birth_date")
                        <td>{{$judge->birth_date}}</td>
                        @endif
                        @if($field_name=="birth_place")
                        <td>{{$judge->birth_place}}</td>
                        @endif
                        @if($field_name=="phone")
                        <td>{{$judge->phone}}</td>
                        @endif
                        @if($field_name=="phone2")
                        <td>{{$judge->phone2}}</td>
                        @endif
                        @if($field_name=="office")
                        <td>{{$judge->office}}</td>
                        @endif
                        @if($field_name=="mobile")
                        <td>{{$judge->mobile}}</td>
                        @endif
                        @if($field_name=="email")
                        <td>{{$judge->email}}</td>
                        @endif
                        @if($field_name=="car_number")
                        <td>{{$judge->car_num}}</td>
                        @endif
                        @if($field_name=="residence")
                        <td>{{$judge->residence}}</td>
                        @endif
                        @if($field_name=="province_id")
                        <td>{{isset($provinces[$judge->province_id])?$provinces[$judge->province_id]:''}}</td>
                        @endif
                        @if($field_name=="district_id")
                        <td>{{isset($districts[$judge->district_id])?$districts[$judge->district_id]:''}}</td>
                        @endif
                        @if($field_name=="zone_id")
                        <td>{{isset($zones[$judge->zone_id])?$zones[$judge->zone_id]:''}}</td>
                        @endif
                        @if($field_name=="date_service")
                        <td>{{$judge->date_service}}</td>
                        @endif
                        @if($field_name=="date_juridation")
                        <td>{{$judge->date_juridation}}</td>
                        @endif
                        @if($field_name=="date_promotion")
                        <td>{{$judge->date_promotion}}</td>
                        @endif
                        @if($field_name=="degree")
                        <td>{{$judge->degree}}</td>
                        @endif
                        @endforeach
                        <td width="10">
                            <a href="{{url('manage_judges') . '/' . $judge->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <hr>
        <div>
            <a href="{{url("manage_judges/create")}}" class="btn btn-default">جديد</a>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
    $(function () {
    $("#dynamic_table").DataTable({
    "dom": 'T<"clear">lfrtip',
            "tableTools": {
            "sSwfPath": "{{url('LTE/plugins/datatables/extensions/TableTools/swf/copy_csv_xls.swf')}}"
            },
            "language": {
            "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
            },
            "encode": {
            "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
            }
    });
    var dateFields = $('input[name="date_service"],input[name="date_juridation"],input[name="date_promotion"],input[name="date_service"]');
    dateFields.daterangepicker({
    autoUpdateInput: false,
            showDropdowns: true,
            locale: {
            cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
            }
    });
    dateFields.on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
    });
    dateFields.on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('#degree').slider({ min: 1, max: 30, value: [{{old('degree') == ''?'1, 30':old('degree')}}], focus: true });
    });
</script>
<style>
    #slider_zone .slider-selection {
        background: #337ab7;
    }
</style>
@endsection