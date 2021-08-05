@extends('layouts.app')

@section('content')
<?php $tab_index = 4 ?>
<?php
$d_judge_court_id = isset($defaults['judge_court_id'])?$defaults['judge_court_id']:0;
$d_month = isset($defaults['month'])?$defaults['month']:0;
$d_year = isset($defaults['year'])?$defaults['year']:0;
?>
<style>
    .width_12{width:12%;display: inline-block;}
</style>
<div class="box">
    <div class="pad">
        @if(isset($count) && $count>0)
        <a href="{{url("monthly_reports/bulk_create")}}" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        <a href="{{url("manage_judgement/".$judge_court_id . '/' . $month . '/' . $year)}}" class="btn btn-app"><i class="fa fa-newspaper-o"></i>الأحكام</a>
        {!! Form::open(['url' => 'monthly_reports/'.$judge_court_id . '/' . $month . '/' . $year, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("monthly_reports")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
        <a class="btn btn-app edit-fields"><i class="fa fa-pencil-square-o"></i>تعديل الخانات</a>
        <a href="#" id="REFRESH" class="btn btn-app"><i class="fa fa-undo"></i>تنشيط</a>
        <a class="btn btn-app add_specialities"><i class="fa fa-plus-square-o"></i>الإختصاصات</a>
        <a href="{{url("swap")}}" class="btn btn-app"><i class="fa fa-arrows-h"></i>مبادلة</a>
        @if(isset($last_report))
        <a href="{{url("monthly_reports/".$last_report[0]->judge_court_id."/".$last_report[0]->month."/".$last_report[0]->year)}}" class="btn btn-app"><i class="fa fa-history"></i>آخر مدخل</a>
        @endif
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
    {!! Form::open(['route' => 'monthly_reports_bulk_save','method' => 'post']) !!}
    <input type="hidden" name="count" id="COUNT" value="{{old('count',isset($count)?$count:1)}}"/>
    <input type="hidden" name="original_judge_court_id" value="{{isset($monthly_report[0])?$monthly_report[0]->judge_court_id:0}}"/>
    <input type="hidden" name="original_judge_id" value="{{isset($monthly_report[0]->judge_id)?$monthly_report[0]->judge_id:0}}"/>
    <input type="hidden" name="original_year" value="{{isset($monthly_report[0]->year)?$monthly_report[0]->year:(($d_year!=0)?$d_year:date('Y'))}}"/>
    <input type="hidden" name="original_month" value="{{isset($monthly_report[0]['month'])?$monthly_report[0]['month']:(($d_month!=0)?$d_month:'')}}"/>
    <input type="hidden" name="court_type" value="{{old('court_type',isset($court_type_id)?$court_type_id:0)}}"/>
    <input type="hidden" name="court_name" value="{{old('court_name',isset($court_name_id)?$court_name_id:0)}}"/>
    <input type="hidden" name="specialities" value=""/>
    <div class="box-body" style="font-size: 12px;">
        <input type='hidden' value='{{isset($monthly_report->id)? $monthly_report->id : 0}}' name='id'/>
        <div class="row form-group">
            <div class='{{$errors->has('judge_court_id')?' has-error':''}}'>
                <label class="col-lg-1 text-left">المحكمة</label>
                <div class="col-lg-5 begin">
                    <select class="form-control selectpicker show-tick show-menu-arrow" title=' -- Select One -- ' data-style="btn-default" data-live-search="true" data-size=10 name="judge_court_id" id='monthlyReportJudgeCourtBulk' data-url="{{url("/")}}">
                        @foreach($judge_courts as $judge_court)
                        @if(isset($courts[$judge_court->court_id]) && isset($judges[$judge_court->judge_id]))
                        <option value="{{$judge_court->id}}" {{(isset($monthly_report[0]->judge_court_id) && ($monthly_report[0]->judge_court_id==$judge_court->id )) || ( (old('judge_court_id') == $judge_court->id) || ( $d_judge_court_id == $judge_court->id) )?"selected":""}}>
                            {{$courts[$judge_court->court_id] . ' | ' . $judges[$judge_court->judge_id] . (isset($retired_judges[$judge_court->judge_id])?" (متقاعد)":"") }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="{{$errors->has('judge_id')?' has-error':''}}">
                <label class="col-lg-1 text-left">القاضي</label>
                <div class="col-lg-5">
                    {!! Form::select('judge_id', $judges, isset($monthly_report[0]->judge_id)?$monthly_report[0]->judge_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default", 'id' => 'judgesDropdown']) !!}
                </div> 
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <div class="{{$errors->has('year')?' has-error':''}}">
                    <label class="col-lg-1 text-left">السنة</label>
                    <div class="col-lg-2">
                        {!! Form::select('year',years(), isset($monthly_report[0]->year)?$monthly_report[0]->year:(($d_year!=0)?$d_year:date('Y')), ['class' => 'form-control selectpicker show-tick show-menu-arrow', 'data-style' => "btn-default",]) !!}
                    </div>
                </div>
                <div class="{{$errors->has('month')?' has-error':''}}">
                    <label class="col-lg-1 text-left">الشهر</label>
                    <div class="col-lg-2">
                        <select name='month' data-style='btn-default' class='form-control selectpicker show-tick show-menu-arrow' data-live-search='true' data-size='10' title=' -- Select One -- '>
                            @foreach(months() as $i => $v)
                            <?php 
                            $selected = [];
                            $id = isset($monthly_report[0]['month'])? $monthly_report[0]['month'] : (($d_month!=0)?$d_month:old('month'));
                            $selected[$id] = 'selected'; 
                            ?>
                            <option value='{{$i}}' {{isset($selected[$i])?$selected[$i]:''}}>{{monthName($v)}} ({{$v}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div> 
        </div>
        <hr>
        
        <div class="row ">
            <div class="col-lg-12 form-group" id="fields_container">
                <div class="text-left">
                    <button class="btn btn-success text-right add_row">
                        <i class="fa fa-plus"></i>
                        <span>Add</span>
                    </button>
                </div>
                

                <table class="fields_table" style="width: 100%;">
                    <thead>
                    <th style="width: 15px;">&nbsp;</th>
                    <th style="text-align: center;">الإختصاص</th>
                    <th style="text-align: center;" class="rotated {{ in_array('rotated',$field_to_hide)?" hidden":"" }}"><span>مدوّر</span></th>
                    <th style="text-align: center;" class="pretencesArrival {{ in_array('pretencesArrival',$field_to_hide)?" hidden":"" }}"><span>وارد إدعاء نيابة</span></th>
                    <th style="text-align: center;" class="arrivalDirectComplaint {{ in_array('arrivalDirectComplaint',$field_to_hide)?" hidden":"" }}"><span>وارد شكوى مباشرة</span></th>
                    <th style="text-align: center;" class="arriving {{ in_array('arriving',$field_to_hide)?" hidden":"" }}"><span>الوارد</span></th>
                    <th style="text-align: center;" class="eliminatedArrival {{ in_array('eliminatedArrival',$field_to_hide)?" hidden":"" }}"><span>الوارد المشطوب</span></th>
                    <th style="text-align: center;" class="totalCases {{ in_array('totalCases',$field_to_hide)?" hidden":"" }}"><span>المجموع العام</span></th>
                    <th style="text-align: center;" class="casesOnSchedule{{ in_array('casesOnSchedule',$field_to_hide)?" hidden":"" }}"><span>على الجدول</span></th>
                    <th style="text-align: center;" class="protectionMeasures{{ in_array('protectionMeasures',$field_to_hide)?" hidden":"" }}"><span>تدابير حماية</span></th>
                    <th style="text-align: center;" class="primaryReport before_auto_titles{{ in_array('primaryReport',$field_to_hide)?" hidden":"" }}"><span>تقرير</span></th>
                    <?php
                    foreach ( old('separated.0',isset($fields)&&!empty($fields)?$fields:[]) as $index => $field)
                    {
                        ?>
                        <th style="text-align: center;">
                            <?php
                            $cfield = is_object($field) ? $field->separated_id : $index;
                            echo $separated[$cfield];
                            ?>
                        </th>
                        <?php
                    }
                    ?>
                        <th style="text-align: center;" class="totalSeparated after_auto_titles{{ in_array('totalSeparated',$field_to_hide)?" hidden":"" }}"><span>مجموع المفصول</span></th>
                        <th style="text-align: center;" class="remainedCases {{ in_array('remainedCases',$field_to_hide)?" hidden":"" }}"><span>الباقي</span></th>
                        <th style="text-align: center;" class="forExecution {{ in_array('forExecution',$field_to_hide)?" hidden":"" }}"><span>محالة للتنفيذ</span></th>
                        <th style="text-align: center;" class="executed {{ in_array('executed',$field_to_hide)?" hidden":"" }}"><span>منفّذة</span></th>
                        <th>&nbsp;</th>
                    </thead>
                    <tbody>
                        
                        @foreach(old('speciality_id',isset($monthly_report)?$monthly_report:[] ) as $index => $report)
                        <tr>
                            <td>
                                <i class="fa fa-arrow-up text-red move-up" role="button"></i><br/>
                                <i class="fa fa-arrow-down text-green move-down" role="button"></i>
                            </td>
                            <td class="{{$errors->has('speciality_id.'.$report)?' has-error':''}}">
                                <input name="order[]" type="hidden" value=""/>
                                <select name='speciality_id[]' data-style='btn-default' class='form-control selectpicker show-tick show-menu-arrow speciality_dp' data-live-search='true' data-size='10' title=' -- Select One -- '>
                                    @foreach($specialities as $i => $v)
                                    <?php 
                                    $selected = [];
                                    $id = is_object($report) ? $report->speciality_id : old('speciality_id.'.$index);
                                    $selected[$id] = 'selected'; 
                                    ?>
                                    <option value='{{$i}}' {{isset($selected[$i])?$selected[$i]:''}}>{{$v}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td id="rotated" class="{{$errors->has('rotated.'.$index)?' has-error':''}}{{ in_array('rotated',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='rotated[]' autocomplete="off" value='{{old('rotated.'.$index,isset($report->rotated)?$report->rotated:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="pretencesArrival" class="{{$errors->has('pretencesArrival')?' has-error':''}}{{ in_array('pretencesArrival',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='pretencesArrival[]' autocomplete="off" value='{{old('pretencesArrival.'.$index,isset($report->pretencesArrival)?$report->pretencesArrival:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="arrivalDirectComplaint" class="{{$errors->has('arrivalDirectComplaint')?' has-error':''}}{{ in_array('arrivalDirectComplaint',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='arrivalDirectComplaint[]' autocomplete="off" value='{{old('arrivalDirectComplaint.'.$index,isset($report->arrivalDirectComplaint)?$report->arrivalDirectComplaint:'')}}' class='form-control indexedField'/>
                            </td>
                            <td id="arriving" class="{{$errors->has('arriving')?' has-error':''}}{{ in_array('arriving',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='arriving[]' autocomplete="off" value='{{old('arriving.'.$index,isset($report->arriving)?$report->arriving:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="eliminatedArrival" class="{{$errors->has('eliminatedArrival')?' has-error':''}}{{ in_array('eliminatedArrival',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='eliminatedArrival[]' autocomplete="off" value='{{old('eliminatedArrival.'.$index,isset($report->eliminatedArrival)?$report->eliminatedArrival:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="totalCases" class="{{$errors->has('totalCases.'.$index)?' has-error':''}}{{ in_array('totalCases',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='totalCases[]' autocomplete="off" value='{{old('totalCases.'.$index,isset($report->totalCases)?$report->totalCases:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="casesOnSchedule" class="{{$errors->has('casesOnSchedule')?' has-error':''}}{{ in_array('casesOnSchedule',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='casesOnSchedule[]' autocomplete="off" value='{{old('casesOnSchedule.'.$index,isset($report->casesOnSchedule)?$report->casesOnSchedule:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="protectionMeasures" class="{{$errors->has('protectionMeasures')?' has-error':''}}{{ in_array('protectionMeasures',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='protectionMeasures[]' autocomplete="off" value='{{old('protectionMeasures.'.$index,isset($report->protectionMeasures)?$report->protectionMeasures:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="primaryReport" class="before_auto_fields{{$errors->has('primaryReport')?' has-error':''}}{{ in_array('primaryReport',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='primaryReport[]' autocomplete="off" value='{{old('primaryReport.'.$index,isset($report->primaryReport)?$report->primaryReport:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <?php
                            foreach ( (old('separated.'.$index,isset($fields)?$fields:[])) as $i=>$field)
                            {
                                ?>
                                <td>
                                    <?php
                                    $cfield = is_object($field)?$field->separated_id:$i;
                                    echo Form::text('separated['.($index+1).']['.$cfield.']', old('separated.'.$index.'.'.$i, (isset($separated_reports[$index][$cfield])?$separated_reports[$index][$cfield]:0) ), ['class' => 'form-control indexedField'])
                                    ?>
                                </td>
                                <?php
                            }
                            ?>
                            <td id="totalSeparated" class="after_auto_fields{{$errors->has('totalSeparated.'.$index)?' has-error':''}}{{ in_array('totalSeparated',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='totalSeparated[]' autocomplete="off" value='{{old('totalSeparated.'.$index,isset($report->totalSeparated)?$report->totalSeparated:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="remainedCases" class="{{$errors->has('remainedCases.'.$index)?' has-error':''}}{{ in_array('remainedCases',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='remainedCases[]' autocomplete="off" value='{{old('remainedCases.'.$index,isset($report->remainedCases)?$report->remainedCases:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="forExecution" class="{{$errors->has('forExecution')?' has-error':''}}{{ in_array('forExecution',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='forExecution[]' autocomplete="off" value='{{old('forExecution.'.$index,isset($report->forExecution)?$report->forExecution:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="executed" class="{{$errors->has('executed')?' has-error':''}}{{ in_array('executed',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='executed[]' autocomplete="off" value='{{old('executed.'.$index,isset($report->executed)?$report->executed:'')}}' class='form-control noPad indexedField'/>
                            </td>
                            <td data-in="{{(old('count',isset($count)?$count:0)-1)}}">
                                <button type="button" class="btn btn-flat btn-danger remove indexedField"><i class="fa fa-fw fa-remove"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if(null==old('speciality_id',isset($monthly_report)?$monthly_report:[]))
                        <tr>
                            <td>
                                <i class="fa fa-arrow-up text-red move-up" role="button"></i><br/>
                                <i class="fa fa-arrow-down text-green move-down" role="button"></i>
                            </td>
                            <td>
                                {!! Form::select('speciality_id[]', $specialities, old('speciality_id.0',0), ['class' => 'form-control indexedField selectpicker show-tick show-menu-arrow speciality_dp','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default"]) !!}
                            </td>
                            <td id="rotated" class="{{$errors->has('rotated')?' has-error':''}}{{ in_array('rotated',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='rotated[]' autocomplete='off' value='{{old('rotated.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="pretencesArrival" class="{{$errors->has('pretencesArrival')?' has-error':''}}{{ in_array('pretencesArrival',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='pretencesArrival[]' autocomplete='off' value='{{old('pretencesArrival.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="arrivalDirectComplaint" class="{{$errors->has('arrivalDirectComplaint')?' has-error':''}}{{ in_array('arrivalDirectComplaint',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='arrivalDirectComplaint[]' autocomplete='off' value='{{old('arrivalDirectComplaint.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="arriving" class="{{$errors->has('arriving')?' has-error':''}}{{ in_array('arriving',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='arriving[]' autocomplete='off' value='{{old('arriving.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="eliminatedArrival" class="{{$errors->has('eliminatedArrival')?' has-error':''}}{{ in_array('eliminatedArrival',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='eliminatedArrival[]' autocomplete='off' value='{{old('eliminatedArrival.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="totalCases" class="{{$errors->has('totalCases')?' has-error':''}}{{ in_array('totalCases',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='totalCases[]' autocomplete='off' value='{{old('totalCases.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="casesOnSchedule" class="{{$errors->has('casesOnSchedule')?' has-error':''}}{{ in_array('casesOnSchedule',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='casesOnSchedule[]' autocomplete='off' value='{{old('casesOnSchedule.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="protectionMeasures" class="{{$errors->has('protectionMeasures')?' has-error':''}}{{ in_array('protectionMeasures',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='protectionMeasures[]' autocomplete="off" value='{{old('protectionMeasures.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="primaryReport" class="before_auto_fields{{$errors->has('primaryReport')?' has-error':''}}{{ in_array('primaryReport',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='primaryReport[]' autocomplete="off" value='{{old('primaryReport.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="totalSeparated" class="after_auto_fields{{$errors->has('totalSeparated')?' has-error':''}}{{ in_array('totalSeparated',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='totalSeparated[]' autocomplete='off' value='{{old('totalSeparated.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="remainedCases" class="{{$errors->has('remainedCases')?' has-error':''}}{{ in_array('remainedCases',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='remainedCases[]' autocomplete='off' value='{{old('remainedCases.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="forExecution" class="{{$errors->has('forExecution')?' has-error':''}}{{ in_array('forExecution',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='forExecution[]' autocomplete='off' value='{{old('forExecution.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td id="executed" class="{{$errors->has('executed')?' has-error':''}}{{ in_array('executed',$field_to_hide)?" hidden":"" }}">
                                <input type='text' name='executed[]' autocomplete='off' value='{{old('executed.0',0)}}' class='form-control noPad indexedField'/>
                            </td>
                            <td>
                                <button type="button" class="btn btn-flat btn-danger remove indexedField"><i class="fa fa-fw fa-remove"></i></button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <hr>
        <div>
            <button type="submit" class="btn btn-default indexedField"><i class="fa fa-save"></i> حفظ</button>
        </div>
        </form>
    </div>
    <script>
     $(function(){
         $(".add_row").on("click", function(e)
         {
            e.preventDefault();
            $('.speciality_dp').selectpicker('destroy');
            var tr = $('table tr:last').clone();
            tr.find('input').val('');
            tr.find('select').val('');
            $('table tbody').append(tr);
            $('.speciality_dp').selectpicker();
            fixSeparatedFieldsIndex();
            $("#COUNT").val(function(i, val){
                return ++val;
            });
        });
        
        var fixSeparatedFieldsIndex = function(){
            $("table tr").each(function(i){
                var index = i;
                $(this).find("input[name^=separated]").each(function(){
                    old_name = $(this).attr('name');
                    new_name = old_name.replace(/\[([0-9]+)\]\[/mg,'['+index+'][');
                    $(this).attr('name',new_name);
                })
            });
        }
        
        $('.fields_table').on('keyup','input.form-control', function(e)
        {
            switch(e.keyCode)
            {
                case 40:
                {
                    $this = $(this);
                    var tr = $this.closest('tr');
                    var td = $this.closest('td');
                    var i = tr.find('td').index( td );
                    
                    tr.next().find('td:eq('+i+') input.form-control').focus();
                }
                break;
                case 38:
                {
                    $this = $(this);
                    var tr = $this.closest('tr');
                    var td = $this.closest('td');
                    var i = tr.find('td').index( td );
                    
                    tr.prev().find('td:eq('+i+') input.form-control').focus();
                }
                break;
                case 39:
                {
                    $this = $(this);
                    var tr = $this.closest('tr');
                    var td = $this.closest('td');
                    var i = tr.find('td').index( td ) - 1;
                    
                    tr.find('td:eq('+i+') input.form-control').focus();
                }
                break;
                case 37:
                {
                    $this = $(this);
                    var tr = $this.closest('tr');
                    var td = $this.closest('td');
                    var i = tr.find('td').index( td ) + 1;
                    
                    tr.find('td:eq('+i+') input.form-control').focus();
                }
                break;
            }
        })
        
        $("table").on("click", ".move-up", function(){
            var current = $(this).closest("tr")
            var prev = current.prev();
            prev.before(current);
            fixSeparatedFieldsIndex();
        })
        
        $("table").on("click", ".move-down", function(){
            var current = $(this).closest("tr")
            var next = current.next();
            next.after(current);
            fixSeparatedFieldsIndex();
        })
        
        $(".edit-fields").on("click", function(e){
            e.preventDefault();
            var court_name = $("input[name=court_name]").val();
            var court_type = $("input[name=court_type]").val();
            
            if(court_name!=0 && court_type!=0)
            {
                var href = "{{url('manage_court_fields')}}/" + court_name + "/" + court_type;
                window.open(href);
            }
            else
            {
                alert("يجب اختيار محكمة");
            }
        })
        
        $(".add_specialities").on("click", function(e){
            var specialities = $("input[name=specialities]").val();
            
            if(specialities=="")
                return false;
            
            $.each(specialities.split(','), function(i,v){
                $(".add_row").click();
                $(".fields_table tr:last").find("select[name^=speciality_id]").selectpicker("val", v)
            })
        });
        
        $("#REFRESH").on("click", function(){
            var judge_court_id = $("select[name=judge_court_id]").val();
            var month = $("select[name=month]").val();
            var year = $("select[name=year]").val();
            
            if(parseInt(judge_court_id) > 0 && parseInt(month) > 0 && parseInt(year) > 0)
            {
                var href = "{{url('monthly_reports')}}/" + judge_court_id + "/" + month + "/" + year;
                window.location = href;
            }
        })
        
        $("table").on("click", ".remove",function(){
            if (confirm('Are you sure you want to delete this record ?'))
            {
                tr = $(this).closest('tr');

               if(tr.prev().index()>0)
                  tr.prev().find(".remove").closest('td').show();

               tr.remove();
               fixSeparatedFieldsIndex();
               $("#COUNT").val(function(i, val){
                   return --val;
               });
            }
        });
        
        $(document).on('keydown', null, 'ctrl+s', function(e){
            e.preventDefault();
            $("button[type=submit]").click();
        });
        
        $(document).on('keydown', null, 'ctrl+d', function(e){
            e.preventDefault();
            $(".add_row").click();
        });
        
        $(".fields_table").on("mouseover","td", function(){
            $(".fields_table td").each(function(){
                $(this).find("input").css('backgroundColor','transparent');
            })
            
            var c = $(this).index();
            $(this).parent("tr").find("td").each(function(){
                $(this).find("input").css('backgroundColor','#d2d6de');
            })
            
            $(".fields_table tr").find("td:eq("+c+")").each(function(){
                $(this).find("input").css('backgroundColor','#d2d6de');
            })
        })
        
        $(".fields_table").on("mouseout","td", function(){
            $(".fields_table td").each(function(){
                $(this).find("input").css('backgroundColor','transparent');
            })
        })
     })
    </script>
    @endsection