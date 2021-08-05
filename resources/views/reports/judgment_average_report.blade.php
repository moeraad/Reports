@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">القاضي</th>
                    <th class="col-lg-1">نوع الحكم</th>
                    <th class="col-lg-1">عدد الأحكام</th>
                    <th class="col-lg-1">متوسط المدّة</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($judgments as $judgment)
                {
                    ?>
                    <tr>
                        <td>{{$judgment->first_name}} {{$judgment->last_name}}</td>
                        <td>
                        <?php
                        if ( !empty($judgment->judgment_type_id) && isset($types[$judgment->judgment_type_id]) )
                            echo $types[$judgment->judgment_type_id];
                        else if ( !empty($judgment->speciality_id) && isset($specialities[$judgment->speciality_id]) )
                            echo $specialities[$judgment->speciality_id];
                        else
                            echo '';
                        ?>
                        </td>
                        <td>{{$judgment->judgements_count}}</td>
                        <td>{{$judgment->average}}</td>
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