@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">ID</th>
                    <th class="col-lg-3">المحكمة</th>
                    <th class="col-lg-1">القاضي</th>
                    <th class="col-lg-1">الوظيفة</th>
                    <th class="col-lg-1">من</th>
                    <th class="col-lg-1">إلى</th>
                    <th class="col-lg-1"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($judge_courts as $judge_court)
                {
                    ?>
                    <tr>
                        <td class="col-lg-3">{{$judge_court->id}}</td>
                        <td class="col-lg-3">{{isset($courts[$judge_court->court_id])?$courts[$judge_court->court_id]:''}}</td>
                        <td class="col-lg-3">{{isset($judges[$judge_court->judge_id])?$judges[$judge_court->judge_id]:''}}</td>
                        <td class="col-lg-2">{{isset($roles[$judge_court->role_id])?$roles[$judge_court->role_id]:''}}</td>
                        <td class="col-lg-2">{{$judge_court->date_from}}</td>
                        <td class="col-lg-2">{{$judge_court->date_to}}</td>
                        <td class="col-lg-1">
                            <a href="{{url('manage_judge_court') . '/' . $judge_court->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div>
            <a href="{{url("manage_judge_court/create")}}" class="btn btn-default">جديد</a>
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