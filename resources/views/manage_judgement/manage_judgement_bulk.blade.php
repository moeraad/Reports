@extends('layouts.app')

@section('content')
<?php
$d_judge_court_id = isset($defaults['judge_court_id']) ? $defaults['judge_court_id'] : 0;
$d_report_date = isset($defaults['report_date']) ? $defaults['report_date'] : 0;
?>
<div class="box">
    <div class="pad">
        @if(isset($count) && $count>0)
        <a href="{{url("manage_judgement/bulk_create")}}" id="NEW_RECORD" class="btn btn-app"><i class="fa fa-file-text"></i>جديد</a>
        <a href="{{url('monthly_reports/'.$judge_court_id . '/' . $month . '/' . $year)}}" class="btn btn-app"><i class="fa fa-newspaper-o"></i>الجدول</a>
        {!! Form::open(['url' => 'manage_judgement/'.$judge_court_id . '/' . $month . '/' . $year, 'method' => 'delete', 'class' => 'inline']) !!}
        <button type='submit' class='btn btn-app'><i class="fa fa-remove"></i>حذف</button>
        {!! Form::close() !!}
        @endif
        <a href="{{url("manage_judgement")}}" class="btn btn-app"><i class="fa fa-list"></i>عرض</a>
        <a href="#" id="REFRESH" class="btn btn-app"><i class="fa fa-undo"></i>تنشيط</a>
        @if(isset($last_report))
        <a href="{{url("manage_judgement/".$last_report[0]->judge_court_id."/".date("m",strtotime($last_report[0]->report_date))."/".date("Y",strtotime($last_report[0]->report_date)))}}" class="btn btn-app"><i class="fa fa-history"></i>آخر مدخل</a>
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

    {!! Form::open(['route' => 'judgments_bulk_save','method' => 'post','name'=>'main']) !!}
    <input type="hidden" name="leave_safe" value="0"/>
    <input type="hidden" name="count" id="COUNT" value="{{old('count',isset($count)?$count:1)}}"/>
    <input type="hidden" name="original_judge_court_id" value='{{isset($judgements[0])?$judgements[0]->judge_court_id:0}}'/>
    <div class="box-body">
        <div class="row form-group">
            <div>
                <div class="form-group{{$errors->has('judge_court_id')?' has-error':''}}">
                    <label class="col-lg-1 text-left">المحكمة</label>
                    <div class="col-lg-8">
                        <select data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow header" data-live-search="true" data-size=10 name="judge_court_id" title=" -- Select One -- " id="judgementJudgeCourtBulk" data-url="{{url("/")}}">
                            @foreach($judge_courts as $judge_court)
                            @if(isset($courts[$judge_court->court_id]) && isset($judges[$judge_court->judge_id]))
                            <option value="{{$judge_court->id}}" {{(isset($judgements[0]->judge_court_id) && ($judgements[0]->judge_court_id == $judge_court->id )) || (old('judge_court_id') == $judge_court->id) || ( $d_judge_court_id == $judge_court->id)?"selected":""}}>{{$courts[$judge_court->court_id]}} | {{$judges[$judge_court->judge_id]}}</option>
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
                        {!! Form::select('report_date', $dates, isset($judgements[0]->report_date)?$judgements[0]->report_date:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true", 'data-size' => 10, "title" => " -- Select One -- ", 'data-style' => "btn-default", 'id' => "REPORT_DATE_FIELD", "data-uri" => url('manage_judgement')]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group{{$errors->has('judge_id')?' has-error':''}}'>
                <label class="col-lg-1 text-left">القاضي</label>
                <div class="col-lg-11" id="judges_container">
                    {!! Form::select('judge_id', $judges, isset($judgements[0]->judge_id)?$judgements[0]->judge_id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow','data-live-search' => "true",'data-size' => 10, "title" => ' -- Select One -- ', 'data-style' => "btn-default"]) !!}
                </div>
            </div>
        </div>
        <hr>
        <div class="row ">
            <div class="col-lg-12 form-group">
                <div class="text-left">
                    <div class="input-group pull-right" style='width: 110px;'>
                        <input type="text" class="form-control add_rows_count" value='1'>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-success add_row"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                    <div class="pull-left col-lg-2 text-right">
                        عدد الأحكام <small class="text-bold text-black">{{isset($total_separated)?$total_separated:0}}</small>/ <span class="text-bold text-green" id="LINES_COUNT">{{isset($count)?$count:0}}</span>
                    </div>
                    <div class='clearfix'></div>
                </div>
                <hr/>
                <table class="fields_table {{$field_to_hide}}" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 25px;" class="text-center ">&nbsp;</th>
                            <th>&nbsp;</th>
                            <th><span>الرقم</span></th>
                            <th class="articles"><span>المواد</span></th>
                            <th class="speciality_id"><span>الإختصاص</span></th>
                            <th class="status_id"><span>طبيعة الحكم</span></th>
                            <th class="judgment_type_id"><span>نتيجة الحكم</span></th>
                            <th class="decision_source"><span>مصدر القرار</span></th>
                            <th><span>تاريخ الورود</span></th>
                            <th><span>عدد الجلسات</span></th>
                            <th><span>الجلسة الختامية</span></th>
                            <th><span>تاريخ الحكم</span></th>
                            <th><span>قاضي</span></th>
                            <th class="notes"><span>ملاحظات</span></th>
                            <th style="width: 25px;">&nbsp;</th>
                            <th style="width: 25px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( ( isset($judgements) && null != $judgements ) || null != old('speciality_id') )
                        @foreach(old('speciality_id',isset($judgements)?$judgements:[] ) as $index => $judgement)
                        <tr>
                            <td class="text-center line_number">
                                {{$index + 1}}
                            </td>
                            <td>
                                <input type="hidden" name="row_id[]" value="{{old('row_id.'.$index,isset($judgement->id)?$judgement->id:'')}}"/>
                                <button type="button" class="btn btn-flat btn-success disabled indexedField"><i class="fa fa-fw fa-save"></i></button>
                            </td>
                            <td>
                                <?php
                                if(!empty($judgement->rule_number))
                                {
                                    echo $judgement->rule_number;
                                    echo Form::hidden('rule_number[]', $judgement->rule_number);
                                }
                                else
                                {
                                    echo Form::hidden('rule_number[]', '');
                                }
                                ?>
                            </td>
                            <td class="articles" nowrap>
                                <?php
                                $current_articles = [];
                                
                                if (isset($judgment_articles[$index]))
                                {
                                    foreach ($judgment_articles[$index] as $judgement_id => $article_id)
                                    {
                                        if(isset($articles[$judgement_id]))
                                        {
                                            $current_articles[] = "<span class='label label-success'>".$articles[$judgement_id]->number." | ".$articles[$judgement_id]->name."</span>";
                                        }                                        
                                    }
                                    echo Form::hidden('articles[0][]', implode(",", array_values($judgment_articles[$index])));
                                }
                                else
                                {
                                    echo Form::hidden('articles[]', '');
                                }
                                echo implode(", ", $current_articles);
                                ?>
                            </td>

                            <td class="speciality_id">
                                <?php
                                if ( !empty($judgement->speciality_id) )
                                {
                                    echo $specialities[ $judgement->speciality_id ];
                                    echo Form::hidden('speciality_id[]', $judgement->speciality_id);
                                }
                                else
                                {
                                    echo Form::hidden('speciality_id[]', 0);
                                }
                                ?>
                            </td>
                            <td class="status_id">
                                <?php
                                if(!empty($judgement->status_id))
                                {
                                    echo $statuses[$judgement->status_id];
                                    echo Form::hidden('status_id[]', $judgement->status_id);
                                }
                                else
                                {
                                    echo Form::hidden('status_id[]', 0);
                                }
                                    
                                ?>
                            </td>
                            <td class="judgment_type_id">
                                <?php
                                if ( !empty($judgement->judgment_type_id) )
                                {
                                    echo $judgement_types[$judgement->judgment_type_id];
                                    echo Form::hidden('judgment_type_id[]', $judgement->judgment_type_id);
                                }
                                else
                                {
                                    echo Form::hidden('judgment_type_id[]', 0);
                                }
                                ?>
                            </td>
                            <td class="decision_source">
                                <?php
                                if(!empty($judgement->decision_source))
                                {
                                    echo $judgement->decision_source;
                                    echo Form::hidden('decision_source[]', $judgement->decision_source);
                                }
                                else
                                {
                                    echo Form::hidden('decision_source[]', '');
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(!empty($judgement->arrival_date))
                                {
                                    echo $judgement->arrival_date;
                                    echo Form::hidden('arrival_date[]', $judgement->arrival_date);
                                }
                                else
                                {
                                    echo Form::hidden('arrival_date[]', 0);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(!empty($judgement->sessions_count))
                                {
                                    echo $judgement->sessions_count;
                                    echo Form::hidden('sessions_count[]', $judgement->sessions_count);
                                }
                                else
                                {
                                    echo Form::hidden('sessions_count[]', 0);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(!empty($judgement->last_session))
                                {
                                    echo $judgement->last_session;
                                    echo Form::hidden('last_session[]', $judgement->last_session);
                                }
                                else
                                {
                                    echo Form::hidden('last_session[]', 0);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(!empty($judgement->judgement_date))
                                {
                                    echo $judgement->judgement_date;
                                    echo Form::hidden('judgement_date[]', $judgement->judgement_date);
                                }
                                else
                                {
                                    echo Form::hidden('judgement_date[]', 0);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($judgement->judge_id))
                                {
                                    echo $judges[$judgement->judge_id];
                                    echo Form::hidden('direct_judge_id[]', $judgement->judge_id);
                                }
                                else
                                {
                                    echo Form::hidden('direct_judge_id[]', 0);
                                }
                                ?>
                            </td>
                            <td class="notes">
                                <?php
                                if (!empty($judgement->notes))
                                {
                                    echo $judgement->notes;
                                    echo Form::hidden('notes[]', $judgement->notes);
                                }
                                else
                                {
                                    echo Form::hidden('notes[]', 0);
                                }
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-flat btn-danger remove indexedField"><i class="fa fa-fw fa-remove"></i></button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-flat btn-success edit indexedField"><i class="fa fa-fw fa-pencil"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                <br/>
                <div class="text-left">
                    <button class="btn btn-success text-right add_row">
                        <i class="fa fa-plus"></i>
                        <span>Add</span>
                    </button>
                </div>
            </div>
        </div>
        <hr>
        <!--            <div>
                        <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
                    </div>-->
    </div>
</form>
</div>
<script>
    var d_report_date = "{{$d_report_date}}";
    var debounce;
    
    
    
    var save = function () {
        tr = $(this).closest('tr');
        var record = tr.find("input,textarea,select").serializeObject();
        var main_data = {judge_court_id: $("select[name=judge_court_id]").val(), report_date: $("select[name=report_date]").val(), judge_id: $("select[name=judge_id]").val(), "_token": $("[name=_token]").val()};

        (function (tr, record, main_data) {
            $.ajax({
                type: "GET",
                url: "{{url('save_judgment_record')}}",
                data: {record, main_data},
                cache: false,
                async: false,
                success: function (data) {
                    if (data.is_error == false)
                    {
                        tr.find("input[name^=row_id]").val(data.id);
                        tr.find("input[name^=edited]").val(0);
                        tr.find(".save").removeClass("btn-danger").addClass("btn-success");
                    } else
                    {
                        tr.find(".save").addClass("btn-danger");
                    }
                }
            });
        })(tr, record, main_data)
    }

    var removeLoadedTrs = function () {
        var loaded_trs = $('table tr.loaded');
        var new_count = $("#COUNT").val() - loaded_trs.size();
        loaded_trs.remove();
        $("#COUNT").val(new_count);
    }

    var clearRow = function(el){
        $this = el;
        $this.find('select[name^=judgment_type_id],select[name^=status_id],select[name^=speciality_id],select[name^=articles],select[name^=direct_judge_id]').selectpicker('destroy')
        $this.find("input").val('');
        $this.find("select").find('option').prop("selected",false);
        $this.find("input[name^=edited]").val(0);
        $this.find(".save").addClass("btn-danger").removeClass('btn-success');
        $this.find('select[name^=judgment_type_id],select[name^=status_id],select[name^=speciality_id],select[name^=articles],select[name^=direct_judge_id]').selectpicker()
    }
    
    $(function () {
        $("window").on("keyup", function (e) {
//            if( e.keyCode == 8 )
//                $('input[name=leave_safe]').val(1);
        })

        $("#judgementJudgeCourtBulk").on("change", function () {
            var judge_court_id = $(this).val();
            var report_date = $("select[name=report_date]").val();

            removeLoadedTrs();

            if (judge_court_id == 0)
                return false;

            $.ajax({
                type: "GET",
                url: "{{url('refresh_judgement_form')}}",
                data: {judge_court_id: judge_court_id, report_date: report_date, "_token": $("[name=_token]").val()},
                cache: false,
                success: function (data) {
                    $("#dates_container").html(data.dates_dp);
                    $("#judges_container").html(data.judges_dp);
                    $('.selectpicker').selectpicker();

                    $(".fields_table").removeClass(function (index, className) {
                        return (className.match (/(^|\s)hide_\S+/g) || []).join(' ');
                    });
                    
                    for (field in data.fields_to_hide)
                    {
                        $('.fields_table').addClass('hide_' + data.fields_to_hide[field]);
                    }

                    if (d_report_date != 0)
                    {
                        $('select[name=report_date]').selectpicker('val', d_report_date);
                    }
                }
            });
        })

        $("#dates_container").on("change", "#REPORT_DATE_FIELD", function (e) {
            $("#LOADER").show();
            $.ajax({
                type: "GET",
                url: "{{url('load_saved_judgements')}}",
                data: {judge_court_id: $("#judgementJudgeCourtBulk").val(), monthly_report_id: $(this).val(), '_token': $("[name=_token]").val()},
                cache: false,
                success: function (data) {

                    var count = data.count;
                    var copy = $($("#entry_form").html());
                    var trs = [];

                    for (var i = 0; i < count; i++)
                    {
                        tr = copy.clone();
                        tr.addClass("loaded");
                        tr.find(".save").removeClass("btn-danger").addClass("btn-success");
                        tr.find("input[name^=edited]").val(0);
                        
                        for(article in data.judgment_articles[i])
                            tr.find('select[name^=articles] option[value='+article+']').attr("selected","selected");
                       
                        for (judgement_records in data.judgements[i])
                        {
                            switch (judgement_records)
                            {
                                case 'id':
                                    tr.find('input[name^=row_id]').val(data.judgements[i]['id']);
                                    break;

                                case 'notes':
                                case 'judgement_date':
                                case 'last_session':
                                case 'sessions_count':
                                case 'arrival_date':
                                case 'decision_source':
                                case 'rule_number':
                                    tr.find('input[name^=' + judgement_records + ']').val(data.judgements[i][judgement_records]);
                                    break;

                                case 'judge_id':
                                        tr.find('select[name^=direct_judge_id] option[value='+data.judgements[i]['judge_id']+']').attr("selected", "selected");
                                    break;

                                case 'judgment_type_id':
                                case 'speciality_id':
                                case 'status_id':
                                        tr.find('select[name^='+judgement_records+'] option[value='+data.judgements[i][judgement_records]+']').attr("selected", "selected");
                                    break;
                            }
                        }
                        
                        trs.push(tr);
                    }
                    
                    $('tbody').append( trs );
                    $("#LOADER").hide();
                    $("#COUNT").val(function (i, val) {
                        return parseInt(val) + parseInt(count);
                    });
                    
                    mskAllFields();
                }
            });
        })

        window.onbeforeunload = function (e) {
            var leave_safe = $('input[name=leave_safe]').val();
            var count = $('#COUNT').val();
            if (leave_safe == 1)
            {
                $('input[name=leave_safe]').val(0);
                var message = "are you sure you want to leave the page ",
                        e = e || window.event;
                // For IE and Firefox
                if (e) {
                    e.returnValue = message;
                }

                // For Safari
                return message;
            }
        }
        
        $("#REFRESH").on("click", function(){
            var judge_court_id = $("select[name=judge_court_id]").val();
            var report_date = $("select[name=report_date]").val();
            
            if(parseInt(judge_court_id) > 0 && null != report_date)
            {
                var date = new Date(report_date);
            
                var month = date.getMonth() + 1;
                var year = date.getFullYear();

                var href = "{{url('manage_judgement')}}/" + judge_court_id + "/" + month + "/" + year;

                window.location = href;
            }
        })
        
        $(".add_row").on("click", function (e) {
            e.preventDefault();
            if (!$("select[name=report_date]").val()) {
                alert("يجب اختيار التاريخ");
                return;
            }
            
            mskAllFields();
            count = $("#COUNT").val();
            num = $(".add_rows_count").val();
            var last_row = $(".fields_table tr:last");
            var entry_form = $("#entry_form").html();
            
            var $this = $(entry_form);
            
            for(var i = 0; i < num; i++)
            {
                var tr = $this.clone();

                tr.removeClass('loaded');
                tr.find("input[name^=row_id]").val('');
                tr.find("input[name^=edited]").val(1);
                tr.find("input[name^=rule_number]").val('');
                
                tr.find("input[name^=sessions_count]").val( last_row.find("input[name^=sessions_count]").val() );
                tr.find("input[name^=decision_source]").val( last_row.find("input[name^=decision_source]").val() );
                tr.find("input[name^=arrival_date]").val( last_row.find("input[name^=arrival_date]").val() );
                tr.find("input[name^=last_session]").val( last_row.find("input[name^=last_session]").val() );
                tr.find("input[name^=judgement_date]").val( last_row.find("input[name^=judgement_date]").val() );

                tr.find("select[name^=speciality_id] option[value='"+last_row.find("input[name^=speciality_id]").val()+"']").attr("selected","selected");
                tr.find("select[name^=status_id] option[value='"+last_row.find("input[name^=status_id]").val()+"']").attr("selected","selected");
                tr.find("select[name^=direct_judge_id] option[value='"+last_row.find("input[name^=direct_judge_id]").val()+"']").attr("selected","selected");
                tr.find("select[name^=judgment_type_id] option[value='"+last_row.find("input[name^=judgment_type_id]").val()+"']").attr("selected","selected");
                
                var values = last_row.find("input[name^=articles]").val();

                if(null !== values && undefined !== values)
                {
                    $.each(values.split(','), function(i,e){
                        tr.find("select[name^=articles] option[value='" + e + "']").attr("selected","selected");
                    });
                }

                tr.find('.save').removeClass("btn-success").addClass("btn-danger");

                $('table').find('select[name^=articles]').each(function(i){
                    $(this).prop('name', 'articles[' + i + '][]');
                })



                $('tbody').append(tr);
                
                tr.find('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });
                
                tr.find('select[name^=judgment_type_id],select[name^=status_id],select[name^=speciality_id],select[name^=articles],select[name^=direct_judge_id]').selectpicker();
                
                $("#COUNT").val(function (i, val) {
                    return ++val;
                });
            }
            
            $("#LINES_COUNT").html( $("#COUNT").val() );
            reNumberingLines();
        });

        $("table").on("change", "input:not(.bs-searchbox input),textarea,select", function () {
            clearTimeout(debounce);
            var $this = $(this).closest('tr');
            $this.find("input[name^=edited]").val(1);
            $this.find('.save').removeClass("btn-success").addClass("btn-danger");
            debounce = setTimeout(function(){
                save.call($this.find('.save'));
            }, 1000);
        })

        $('.fields_table').on('keyup', 'input.form-control', function (e) {
            switch (e.keyCode)
            {
                case 40:
                    {
                        $this = $(this);
                        var tr = $this.closest('tr');
                        var td = $this.closest('td');
                        var i = tr.find('td').index(td);

                        tr.next().find('td:eq(' + i + ') .form-control').focus();
                    }
                    break;
                case 38:
                    {
                        $this = $(this);
                        var tr = $this.closest('tr');
                        var td = $this.closest('td');
                        var i = tr.find('td').index(td);

                        tr.prev().find('td:eq(' + i + ') .form-control').focus();
                    }
                    break;
                case 39:
                    {
                        $this = $(this);
                        var tr = $this.closest('tr');
                        var td = $this.closest('td');
                        var i = tr.find('td').index(td) - 1;

                        tr.find('td:eq(' + i + ') .form-control').focus();
                    }
                    break;
                case 37:
                    {
                        $this = $(this);
                        var tr = $this.closest('tr');
                        var td = $this.closest('td');
                        var i = tr.find('td').index(td) + 1;

                        tr.find('td:eq(' + i + ') .form-control').focus();
                    }
                    break;
            }
        })

        $("table").on("click", ".save", save);
        
        $("table").on("click", ".edit", function (e) {
            e.preventDefault();
            
            var entry_form = $("#entry_form").html();
            var tr = $(this).closest('tr');
            var $this = $(entry_form);
            
            if( tr.find("select[name^=speciality_id]").size() > 0 )
            {
                maskFields.call($(this));
                return false;
            }
            
            $this.find("input[name^=row_id]").val( tr.find("input[name^=row_id]").val() );
            $this.find("input[name^=edited]").val(0);
            $this.find("input[name^=rule_number]").val( tr.find("input[name^=rule_number]").val() );
            $this.find("input[name^=sessions_count]").val( tr.find("input[name^=sessions_count]").val() );
            $this.find("input[name^=decision_source]").val( tr.find("input[name^=decision_source]").val() );
            $this.find("input[name^=arrival_date]").val( tr.find("input[name^=arrival_date]").val() );
            $this.find("input[name^=last_session]").val( tr.find("input[name^=last_session]").val() );
            $this.find("input[name^=judgement_date]").val( tr.find("input[name^=judgement_date]").val() );
            
            $this.find("select[name^=speciality_id] option[value='"+tr.find("input[name^=speciality_id]").val()+"']").attr("selected", "selected");
            $this.find("select[name^=status_id] option[value='"+tr.find("input[name^=status_id]").val()+"']").attr("selected", "selected");
            $this.find("select[name^=direct_judge_id] option[value='"+tr.find("input[name^=direct_judge_id]").val()+"']").attr("selected", "selected");
            $this.find("select[name^=judgment_type_id] option[value='"+tr.find("input[name^=judgment_type_id]").val()+"']").attr("selected", "selected");

            var values = tr.find("input[name^=articles]").val();
            
            $this.find("select[name^=articles] option:selected").prop("selected",false);
            if(null !== values)
            {
                $.each(values.split(','), function(i,e){
                    $this.find("select[name^=articles] option[value='" + e + "']").prop("selected", true);
                });
            }
            
            tr.replaceWith($this);
            reNumberingLines();
            
            $this.find('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            $this.find('select[name^=articles],select[name^=direct_judge_id],select[name^=judgment_type_id],select[name^=status_id],select[name^=speciality_id]').selectpicker();
            
            
        });
        
        var maskFields = function(){
            var masked_entry = $("#masked_entry").html();
            var tr = $(this).closest('tr');
            var $this = $(masked_entry);
            
            $this.find('select[name^=articles],select[name^=direct_judge_id],select[name^=judgment_type_id],select[name^=status_id],select[name^=speciality_id]').selectpicker('destroy');
            
            $this.find("input[name^=row_id]").val( tr.find("input[name^=row_id]").val() );
            $this.find("input[name^=edited]").val(0);
            $this.find("input[name^=rule_number]").val( tr.find("input[name^=rule_number]").val() ).after(tr.find("input[name^=rule_number]").val());
            $this.find("input[name^=sessions_count]").val( tr.find("input[name^=sessions_count]").val() ).after(tr.find("input[name^=sessions_count]").val());
            $this.find("input[name^=decision_source]").val( tr.find("input[name^=decision_source]").val() ).after(tr.find("input[name^=decision_source]").val());
            $this.find("input[name^=arrival_date]").val( tr.find("input[name^=arrival_date]").val() ).after(tr.find("input[name^=arrival_date]").val());
            $this.find("input[name^=last_session]").val( tr.find("input[name^=last_session]").val() ).after(tr.find("input[name^=last_session]").val());
            $this.find("input[name^=judgement_date]").val( tr.find("input[name^=judgement_date]").val() ).after(tr.find("input[name^=judgement_date]").val());
            
            $this.find("input[name^=speciality_id]").val(tr.find("select[name^=speciality_id]").val()).after( tr.find("select[name^=speciality_id] option:selected").text() );
            $this.find("input[name^=status_id]").val(tr.find("select[name^=status_id]").val()).after( tr.find("select[name^=status_id] option:selected").text() );
            $this.find("input[name^=direct_judge_id]").val(tr.find("select[name^=direct_judge_id]").val()).after( tr.find("select[name^=direct_judge_id] option:selected").text() );
            $this.find("input[name^=judgment_type_id]").val(tr.find("select[name^=judgment_type_id]").val()).after( tr.find("select[name^=judgment_type_id] option:selected").text() );

            var values = $this.find("select[name^=articles]").val();
            
            if(null !== values)
            {
                $this.find("input[name^=articles]").val( tr.find("select[name^=articles]").val() );
            }
            
            tr.find("select[name^=articles] option:selected").each(function(i,v){
                console.log(arguments);
                $this.find("input[name^=articles]").closest('td').append((i>0?", ":"") + "<span class='label label-success'>"+$(v).text()+"</span>");
            });
            
            
            
            tr.replaceWith($this);
            reNumberingLines();
        }
        
        var mskAllFields = function(){
            $(".fields_table").find("input[name^=edited][value=1]").each(function(){
                $(this).closest('td').find(".save").click();
            })
            
            $(".fields_table").find("input[name^=edited][value=0]").each(function(){
                maskFields.call($(this).closest('td'));
            })
            
            reNumberingLines();
        }
        
        $("table").on("click", ".remove", function () {
            var row_count = $("table tbody tr").size();
            var tr = $(this).closest('tr')
            var row_id = tr.find("input[name^=row_id]").val();
            
            if (!row_id)
            {
                if (tr.prev().index() > 0)
                    tr.prev().find(".remove").closest('td').show();
                
                if(row_count>1)
                {
                    tr.remove();
                    $("#COUNT").val(function (i, val) {
                        return --val;
                    });
                    
                    $("#LINES_COUNT").html( $("#COUNT").val() );
                }
            } else 
            {
                data = {"id": row_id, "_token": $("[name=_token]").val()};
                if (confirm('Are you sure you want to delete this record ?'))
                {
                    (function (tr, data) {
                        $.ajax({
                            type: "GET",
                            url: "{{url('delete_judgment_record')}}",
                            data: data,
                            cache: false,
                            success: function (data) {
                                if (data.is_error == false)
                                {
                                    if (tr.prev().index() > 0)
                                        tr.prev().find(".remove").closest('td').show();
                                    
                                    if(row_count>1)
                                    {
                                        tr.remove();
                                        $("#COUNT").val(function (i, val) {
                                            return --val;
                                        });
                                        
                                        $("#LINES_COUNT").html( $("#COUNT").val() );
                                    }
                                    else
                                    {
                                        clearRow(tr);
                                    }
                                } else
                                {
                                    tr.find(".save").addClass("btn-danger");
                                }
                            }
                        });
                    })(tr, data)
                }
            }
        });

        $("table").on("click", ".edit", function () {
            var entry_form = $("#entry_form").html();
            var $this = $(entry_form);
            
            var row_count = $("table tbody tr").size();
            var tr = $(this).closest('tr')
            
        });

        $(document).on('keydown', null, 'ctrl+d', function (e) {
            e.preventDefault();
            $("button.add_row:first").click();
        });

        $(document).on('keydown', null, 'shift+n', function (e) {
            e.preventDefault();
            $("#NEW_RECORD")[0].click();
        });

        $('table').on('keydown', 'input,select,button.dropdown-toggle', 'esc', function (e) {
            e.preventDefault();
            var $this = $(this);
            
            if( $this.is("input") )
            {
                $this.val('');
            }
            else if($this.is("select"))
            {
                $this.find("option").prop("selected",false)
            }
            else if($this.is("button"))
            {
                $this.parent().find("select").selectpicker('deselectAll')
            }
        });
        
        function reNumberingLines()
        {
            $(".fields_table tr").each(function(i,v){
                $(this).find(".line_number").html(i);
            })
        }
        
        $("#judgementJudgeCourtBulk").trigger('change');
    })
</script>
<script type="text/html" id="entry_form">
<tr>
    <td class="text-center line_number">

    </td>
    <td>
        <input type="hidden" name="row_id[]" value="" />
        <input type="hidden" name="edited[]" value="1"/>
        <button type="button" class="btn btn-flat btn-danger save indexedField"><i class="fa fa-fw fa-save"></i></button>
    </td>
    <td>
        <input type="text" name="rule_number[]" value="" class="form-control" />
    </td>
    <td class="articles">
        <?php
        $current_dp_articles = str_replace('{{$index}}', 0, $dp_articles);
        echo $current_dp_articles;
        ?>
    </td>
    <td class="speciality_id">
        <select name="speciality_id[]" data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow speciality_dp" data-live-search="true" data-size="10" title=" -- Select One -- ">
            @foreach($specialities as $i => $v)
            <?php
            $selected = [];
            $id = isset($judgements->speciality_id) ? $judgements->speciality_id : "";
            $selected[$id] = "selected";
            ?>
            <option value="{{$i}}">{{$v}}</option>
            @endforeach
        </select>
    </td>
    <td class="status_id">
        <select name="status_id[]" data-style="btn-default" class="form-control selectpicker show-tick show-menu-arrow speciality_dp" data-live-search="true" data-size="10" title=" -- Select One -- ">
            @foreach($statuses as $i => $v)
            <?php
            $selected = [];
            $id = isset($judgements->status_id) ? $judgements->status_id : "";
            $selected[$id] = "selected";
            ?>
            <option value="{{$i}}">{{$v}}</option>
            @endforeach
        </select>
    </td>
    <td class="judgment_type_id">
        <?php
        echo $dp_judgments;
        ?>
    </td>
    <td  class="decision_source">
        <input type="text" name="decision_source[]" value="" class="form-control"/>
    </td>
    <td>
        <input type="text" name="arrival_date[]" value="" class="form-control datepicker text-right" autocomplete="off"/>
    </td>
    <td>
        <input type="text" name="sessions_count[]" value="" class="form-control" />
    </td>
    <td>
        <input type="text" name="last_session[]" value="" class="form-control datepicker text-right" autocomplete="off"/>
    </td>
    <td>
        <input type="text" name="judgement_date[]" value="" class="form-control datepicker text-right"/>
    </td>
    <td>
        {!! Form::select("direct_judge_id[]", $judges, "", ["class" => "form-control selectpicker show-tick show-menu-arrow","data-live-search" => "true","data-size" => 10, "title" => " -- Select One -- ", "data-style" => "btn-default"]) !!}
    </td>
    <td>
        <input type="text" name="notes[]" value="" class="form-control" />
    </td>
    <td>
        <button type="button" class="btn btn-flat btn-danger remove indexedField"><i class="fa fa-fw fa-remove"></i></button>
    </td>
    <td>
        <button type="button" class="btn btn-flat btn-primary edit indexedField"><i class="fa fa-fw fa-check"></i></button>
    </td>
</tr>
</script>
<script type="text/html" id="masked_entry">
<tr>
    <td class="text-center line_number">
        
    </td>
    <td>
        <input type="hidden" name="row_id[]" value="0"/>
        <button type="button" class="btn btn-flat btn-success disabled indexedField"><i class="fa fa-fw fa-save"></i></button>
    </td>
    <td>
        {!! Form::hidden('rule_number[]', '') !!}
    </td>
    <td class="articles" nowrap>
        {!! Form::hidden('articles[]', '') !!}
    </td>
    <td class="speciality_id">
         {!! Form::hidden('speciality_id[]', 0) !!}
    </td>
    <td class="status_id">
         {!! Form::hidden('status_id[]', 0) !!}
    </td>
    <td class="judgment_type_id">
         {!! Form::hidden('judgment_type_id[]', 0) !!}
    </td>
    <td class="decision_source">
         {!! Form::hidden('decision_source[]', '') !!}
    </td>
    <td>
         {!! Form::hidden('arrival_date[]', '') !!}
    </td>
    <td>
         {!! Form::hidden('sessions_count[]', 0) !!}
    </td>
    <td>
         {!! Form::hidden('last_session[]', '') !!}
    </td>
    <td>
         {!! Form::hidden('judgement_date[]', '') !!}
    </td>
    <td>
         {!! Form::hidden('direct_judge_id[]', 0) !!}
    </td>
    <td class="notes">
         {!! Form::hidden('notes[]', '') !!}
    </td>
    <td>
        <button type="button" class="btn btn-flat btn-danger remove indexedField"><i class="fa fa-fw fa-remove"></i></button>
    </td>
    <td>
        <button type="button" class="btn btn-flat btn-success edit indexedField"><i class="fa fa-fw fa-pencil"></i></button>
    </td>
</tr>
</script>
@endsection
<?php
$end_time = microtime(true);
//dd( $end_time - $start_time );
?>