@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">العنوان</th>
                    <th class="col-lg-1">الإسم</th>
                    <th class="col-lg-1">الدرجة</th>
                    <th class="col-lg-1">النوع</th>
                    <th class="col-lg-1">الغرفة</th>
                    <th class="col-lg-1">المحافظة</th>
                    <th class="col-lg-1">القضاء</th>
                    <th class="col-lg-1">المنطقة</th>
                    <th class="col-lg-1"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($courts as $court)
                {
                    ?>
                    <tr>
                        <td class="col-lg-4">{{$court->title}}</td>
                        <td class="col-lg-1">{{isset($names[$court->court_name_id])?$names[$court->court_name_id]:''}}</td>
                        <td class="col-lg-1">{{isset($degrees[$court->court_degree_id])?$degrees[$court->court_degree_id]:''}}</td>
                        <td class="col-lg-1">{{isset($types[$court->court_type_id])?$types[$court->court_type_id]:''}}</td>
                        <td class="col-lg-1">{{$court->room}}</td>
                        <td class="col-lg-1">{{isset($provinces[$court->province_id])?$provinces[$court->province_id]:''}}</td>
                        <td class="col-lg-1">{{isset($districts[$court->district_id])?$districts[$court->district_id]:''}}</td>
                        <td class="col-lg-1">{{isset($zones[$court->zone_id])?$zones[$court->zone_id]:''}}</td>
                        <td class="col-lg-1">
                            <a href="{{url('manage_courts') . '/' . $court->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div>
            <a href="{{url("manage_courts/create")}}" class="btn btn-default">جديد</a>
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