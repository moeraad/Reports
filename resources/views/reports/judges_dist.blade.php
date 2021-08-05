@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-1">محافظة</th>
                    <th class="col-lg-1">وظيفة</th>
                    <th class="col-lg-3">عدد</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($judges_dist as $dist)
                {
                    ?>
                    <tr>
                        <td>{{$dist->province}}</td>
                        <td>{{$dist->role}}</td>
                        <td>{{$dist->judges_count}}</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
    $(function () {
        $("#dynamic_table").DataTable({
                "language": {
                    "dom": 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "{{url('LTE/plugins/datatables/extensions/TableTools/swf/copy_csv_xls.swf')}}"
                    },
                    "language": {
                        "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
                    }
                }
            });
    });
</script>
@endsection