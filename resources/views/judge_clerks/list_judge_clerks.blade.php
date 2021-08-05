@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">الكاتب</th>
                    <th class="col-lg-1">القاضي</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($judge_clerks as $judge_clerk)
                {
                    ?>
                    <tr>
                        <td class="col-lg-3">{{$clerks[$judge_clerk->clerk_id]}}</td>
                        <td class="col-lg-8">{{$judges[$judge_clerk->judge_id]}}</td>
                        <td class="col-lg-1">
                            <a href="{{url('judge_clerks') . '/' . $judge_clerk->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div>
            <a href="{{url("judge_clerks/create")}}" class="btn btn-default">جديد</a>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
    $(function () {
        $("#dynamic_table").DataTable({
                "language": {
                    "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
                }
            });
    });
</script>
@endsection