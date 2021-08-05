@extends('layouts.app')

@section('content')
<div class="box">
    <div style="overflow-x: scroll;">
        <div class="row">
            <div class="col-md-2" id="FILTERS">
                {!! Form::open(['route' => 'judgments_bulk_save','method' => 'post','name'=>'main']) !!}
                <div class="col-md-12">
                    <h1 class="text-center">&nbsp;</h1>
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">فلترة النتائج</h3>
                        </div><!-- /.box-header -->
                        <div>
                            <div class="box-group" id="accordion">
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#province" aria-expanded="true" class="collapsed">
                                                المحافظة
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="province" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($provinces as $id => $province)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="province" data-id="{{$id}}">{{$province}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#district" aria-expanded="false" class="collapsed">
                                                القضاء
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="district" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($districs as $id => $distric)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="district" data-id="{{$id}}">{{$distric}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#name" aria-expanded="false" class="collapsed">
                                                إسم المحكمة
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="name" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($names as $id => $name)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="name" data-id="{{$id}}">{{$name}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#type" aria-expanded="false" class="collapsed">
                                                نوع المحكمة
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="type" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($types as $id => $type)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="type" data-id="{{$id}}">{{$type}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#degree" aria-expanded="false" class="collapsed">
                                                درجة المحكمة
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="degree" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($degrees as $id => $degree)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="degree" data-id="{{$id}}">{{$degree}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#speciality" aria-expanded="false" class="collapsed">
                                                الإختصاص
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="speciality" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($specialities as $id => $speciality)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="speciality" data-id="{{$id}}">{{$speciality}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#judges" aria-expanded="false" class="collapsed">
                                                القضاة
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="judges" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div>
                                            <ul class="filter-list">
                                                <?php
                                                foreach ($judges as $id => $judge)
                                                {
                                                    ?>
                                                    <li data-selected="off" data-type="judge" data-id="{{$id}}">{{$judge}}</li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div><!-- /. -->
                    </div><!-- /.box -->
                </div>
                </form>
            </div>
            <div class="col-md-10" id="report_ajax">

            </div>
        </div>
    </div><!-- /. -->
</div><!-- /.box -->    
<script>
    $(function () {
        $("#LOADER").show();
        $.ajax({
            type: "POST",
            url: "../load_full_report",
            data: {filters: {}, "_token": $("[name=_token]").val()},
            cache: false,
            success: function (data) {
                $("#report_ajax").html(data);
                $("#LOADER").hide();
            }
        });

        $("#FILTERS ul li").on("click", function () {
            if ($(this).data("selected") == "off")
            {
                $(this).data("selected", "on");
                $(this).attr("checked", "checked");
            } else if ($(this).data("selected") == "on")
            {
                $(this).data("selected", "off");
                $(this).removeAttr("checked");
            }

            getFilters();
        });

        function getFilters()
        {
            var filters = {};

            $("#FILTERS ul li").each(function () {
                var selected = $(this).data("selected");
                if (selected == "on")
                {
                    var type = $(this).data("type");
                    var id = $(this).data("id");

                    if (!filters[type])
                        filters[type] = [];

                    filters[type].push(id);
                }
            });
            
            $("#LOADER").show();
            $.ajax({
                type: "POST",
                url: "../load_full_report",
                data: {filters: filters, "_token": $("[name=_token]").val()},
                cache: false,
                success: function (data) {
                    $("#LOADER").hide();
                    $("#report_ajax").html(data);
                }
            });
        }
    })
</script>
@endsection