@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body" style="overflow-x: scroll;">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th>الإسم</th>
                    <th>الوظيفة</th>
                    <th>النوع</th>
                    <th>الدرجة</th>
                    <th>الطائفة</th>
                    <th>المحافظة</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($judges as $judge)
                <tr>
                    <td>{{$judge->first_name}} {{$judge->middle_name}} {{$judge->last_name}}</td>
                    <td>{{$roles[$judge->role_id]}}</td>
                    <td>{{@$types[$judge->court_type_id]}}</td>
                    <td>{{@$degree[$judge->court_degree_id]}}</td>
                    <td>{{$judge->sect}}</td>
                    <td>{{@$province[$judge->province_id]}}</td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
    $(function () {
        $("#dynamic_table").DataTable({
                "processing": true,
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "{{url('LTE/plugins/datatables/extensions/TableTools/swf/copy_csv_xls.swf')}}"
                },
                "language": {
                    "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
                }
            });
    });
</script>
@endsection