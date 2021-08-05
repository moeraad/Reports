@extends('layouts.app')

@section('content')
<?php
foreach ($stats as $name => $arr)
{
    $total_judgements = 0;
    $total_reports = 0;
    $reports_count = 0;
    $judgments_count = 0;
    ?>
    <div class="box box-solid box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ucfirst($name)}}</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped"  id="dynamic_table">
                <thead class="panel-heading">
                    <tr>
                        <th class="col-lg-1">تاريخ الجدول</th>
                        <th class="col-lg-1">الأحكام</th>
                        <th class="col-lg-1">الإختصاصات</th>
                        <th class="col-lg-1">الجداول الشهرية</th>
                        <th class="col-lg-1">جداول الأحكام</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($arr as $date => $stat)
                    {
                        $judgements = isset($stat["judgement"]) ? $stat["judgement"] : 0;
                        $reports = isset($stat["report"]) ? $stat["report"] : 0;
                        $count = isset($stat["count"]) ? $stat["count"] : 0;
                        $nb_judgement_tbls = isset($stat["nb_judgement_tbls"]) ? $stat["nb_judgement_tbls"] : 0;

                        $total_judgements += $judgements;
                        $total_reports += $reports;
                        $reports_count += $count;
                        $judgments_count += $nb_judgement_tbls;
                        ?>
                        <tr>
                            <td>{{$date}}</td>
                            <td>{{$judgements}}</td>
                            <td>{{$reports}}</td>
                            <td>{{$count}}</td>
                            <td>{{$nb_judgement_tbls}}</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>المجموع</th>
                        <th>{{$total_judgements}}</th>
                        <th>{{$total_reports}}</th>
                        <th>{{$reports_count}}</th>
                        <th>{{$judgments_count}}</th>
                    </tr>
                </tfoot>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
    <?php
}
?>
@endsection