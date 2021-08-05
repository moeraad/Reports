@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">الإسم</th>
                    <th class="col-lg-1">الجنس</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($clerks as $clerk)
                {
                    ?>
                    <tr>
                        <td class="col-lg-3">{{$clerk->first_name}} {{$clerk->last_name}}</td>
                        <td class="col-lg-8">{{$clerk->sex=="m"?"ذكر":$clerk->sex=="f"?"أنثى":"- غير محدد -"}}</td>
                        <td class="col-lg-1">
                            <a href="{{url('clerk') . '/' . $clerk->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div>
            <a href="{{url("clerk/create")}}" class="btn btn-default">جديد</a>
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