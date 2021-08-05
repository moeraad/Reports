@extends('layouts.app')

@section('content')
<div class="box box-solid box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-1">تاريخ</th>
                    <th class="col-lg-1">المحكمة</th>
                    <th class="col-lg-1">الشهر</th>
                    <th class="col-lg-1">عدد الأحكام</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($monthly_reports as $monthly_report)
                {
                    if(isset($courts[$monthly_report->judge_court_id]))
                    {
                        $dt= \Carbon\Carbon::createFromTimestamp(strtotime($monthly_report->created_at));
                        ?>
                        <tr>
                            <td dir="ltr" align="right"><span class="label label-default">{{$monthly_report->created_at}}</span><br/>{{$dt->diffForHumans()}}</td>
                            <td><span class="label label-default">{{$courts[$monthly_report->judge_court_id]->judge}}</span><br/>{{$courts[$monthly_report->judge_court_id]->title}}</td>
                            <td><span class="label label-default">{{monthName(\Carbon\Carbon::createFromFormat("Y-m-d", $monthly_report->monthly_date)->month)}}</span><br/>{{$monthly_report->monthly_date}}</td>
                            <td><b>{{$monthly_report->judgements}}</b></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
@endsection