@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body" style="overflow-x: scroll;">
        <table class="table table-bordered table-striped"  id="dynamic_table" border="1">
            <thead class="panel-heading">
                <tr>
                    <th>الإسم</th>
                    <th>النوع</th>
                    <th>الدرجة</th>
                    <th>الإختصاص</th>
                    <th>المحافظة</th>
                    <th>القضاء</th>
                    <th>المنطقة</th>
                    <th>الوارد</th>
                    <th>المدور</th>
                    <th>الباقي</th>
                    <th>عدد الدعاوى</th>
                    <th>مجموع المفصول</th>
                    <?php
                    foreach($separated as $separated_item)
                    {
                        ?>
                        <th>{{$separated_item}}</th>
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    @foreach($row as $item)
                    <td>{{$item}}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
//    $(function () {
//        $("#dynamic_table").DataTable({
//                "processing": true,
//                "dom": 'T<"clear">lfrtip',
//                "tableTools": {
//                    "sSwfPath": "{{url('LTE/plugins/datatables/extensions/TableTools/swf/copy_csv_xls.swf')}}"
//                },
//                "language": {
//                    "url": "{{asset('LTE/plugins/datatables/language/Arabic.json')}}"
//                }
//            });
//    });
</script>
@endsection