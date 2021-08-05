@extends('layouts.app')
@section('content')
<div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ملف القاضي
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <a href='{{url('manage_judges/'.$judge->id)}}'><i class='fa fa-pencil'></i></a>
                        <img class="profile-user-img img-responsive img-circle" src="{{$photo}}" alt="User profile picture" width="128">

                        <h3 class="profile-username text-center">{{$judge->first_name}} {{$judge->middle_name}} {{$judge->last_name}}</h3>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>مكان الولادة</b> <a>{{$judge->birth_place}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>تاريخ الولادة</b> <a>{{$judge->birth_date}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>مكان الإقامة</b> <a>{{$judge->residence}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>الدرجة</b> <a>{{$judge->degree}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>الدرجة المقبلة</b> <a>{{$judge->date_promotion}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>رقم السيارة</b> <a>{{$judge->car_num}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>نوع السيارة</b> <a>{{$judge->car_type}}</a>
                            </li>
                        </ul>
                        <label>المحاكم الحالية و السابقة</label>
                        <ul class="list-group list-group-unbordered">
                            @foreach($myCourts as $judge_court)
                                <li class="list-group-item">
                                    <i class="fa fa-circle text-green" style="font-size: 12px;"></i>
                                    <small>{{isset($judge_court->Court->title)?$judge_court->Court->title:''}}</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- About Me Box -->

                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box">
                    <div class="box-header with-border">
                        <h3>معدل الأحكام</h3>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <th>نوع الحكم</th>
                        <th>عدد الأحكام</th>
                        <th>متوسط الأحكام</th>
                        </thead>
                        <tbody>
                            @foreach($judgments as $judgment)
                            <tr>
                                <td>
                                    <?php
                                    if (!empty($judgment_types[$judgment->judgment_type]))
                                        echo $judgment_types[$judgment->judgment_type];
                                    else if (!empty ($specialities[$judgment->judgment_type]))
                                        echo $specialities[$judgment->judgment_type];
                                    else 
                                        echo '';
                                    ?>
                                </td>
                                <td>{{$judgment->judgements_count}}</td>
                                <td>{{ceil($judgment->average/$judgment->judgements_count)}}</td>
                            </tr>
                            @endforeach
                            <tr></td>
                        </tbody>
                    </table> 
                </div>
                <div class="box">
                    <div class="box-header with-border">
                        <h3>تفصيل الأحكام</h3>
                    </div>
                    <div style='overflow-x: scroll;'>
                        <table class="table table-bordered table-striped"  id="dynamic_table">
                            <thead>
                            <th>النوع</th>
                            <th>الدرجة</th>
                            <th>الإختصاص</th>
                            <th>المحافظة</th>
                            <th>القضاء</th>
                            <th>المنطقة</th>
                            <th>عدد الدعاوى</th>
                            <th>مجموع المفصول</th>
                            <?php
                            foreach ($separated as $separated_item)
                            {
                                ?>
                                <th>{{$separated_item}}</th>
                                <?php
                            }
                            ?>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($data as $key => $item)
                                {
                                    foreach ($item as $sKey => $report_separated)
                                    {
                                        ?>
                                        <tr>
                                            <td>{{isset($types[$judge_courts[$key]['court']['court_type_id']])?$types[$judge_courts[$key]['court']['court_type_id']]:''}}</td>
                                            <td>{{isset($degrees[$judge_courts[$key]['court']['court_degree_id']])?$degrees[$judge_courts[$key]['court']['court_degree_id']]:''}}</td>
                                            <td>{{isset($specialities[$sKey])?$specialities[$sKey]:''}}</td>
                                            <td>{{isset($provinces[$judge_courts[$key]['court']['province_id']])?$provinces[$judge_courts[$key]['court']['province_id']]:''}}</td>
                                            <td>{{isset($districs[$judge_courts[$key]['court']['district_id']])?$districs[$judge_courts[$key]['court']['district_id']]:''}}</td>
                                            <td>{{isset($zones[$judge_courts[$key]['court']['zone_id']])?$zones[$judge_courts[$key]['court']['zone_id']]:''}}</td>
                                            <td>{{$report_separated["total_cases"]}}</td>
                                            <td>{{$report_separated["total_separated"]}}</td>
                                            <?php
                                            foreach ($separated as $separated_id => $separated_item)
                                            {
                                                ?>
                                                <td>{{isset($report_separated['separated'][$separated_id])?$report_separated['separated'][$separated_id]:0}}</td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
@endsection