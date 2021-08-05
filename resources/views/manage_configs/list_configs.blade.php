@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">المحافظة</th>
                    <th class="col-lg-1">القضاء</th>
                    <th class="col-lg-1">المنطقة</th>
                    <th class="col-lg-1">الدرجة</th>
                    <th class="col-lg-1">الوظيفة</th>
                    <th class="col-lg-1">العدد</th>
                    <th class="col-lg-1"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($configs as $config)
                {
                    ?>
                    <tr>
                        <td class="col-lg-4">{{$config->province_id}}</td>
                        <td class="col-lg-1">{{$config->district_id}}</td>
                        <td class="col-lg-1">{{$config->zone_id}}</td>
                        <td class="col-lg-1">{{$config->court_degree_id}}</td>
                        <td class="col-lg-1">{{$config->role_id}}</td>
                        <td class="col-lg-1">{{$config->count}}</td>
                        <td class="col-lg-1">
                            <a href="{{url('manage_configs') . '/' . $config->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <hr>
        <div>
            <a href="{{url("manage_configs/create")}}" class="btn btn-default">جديد</a>
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