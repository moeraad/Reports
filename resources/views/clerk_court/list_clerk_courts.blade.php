@extends('layouts.app')

@section('content')
<div class="box">
    <div class="box-body">
        <table class="table table-bordered table-striped"  id="dynamic_table">
            <thead class="panel-heading">
                <tr>
                    <th class="col-lg-3">المحكمة</th>
                    <th class="col-lg-8">الإسم</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($clerk_courts as $clerk_court)
                {
                    if($clerk_court->court_id!=null)
                    {
                        ?>
                        <tr>
                            <td class="col-lg-3">{{$courts[$clerk_court->court_id]}}</td>
                            <td class="col-lg-8">
                                <?php
                                $clerks_ids = explode(",", $clerk_court->clerks_ids);

                                foreach ($clerks_ids as $clerk_id)
                                {
                                    ?>
                                    <span class='label label-success'>{{$clerks[$clerk_id]}}</span>
                                    <?php
                                }
                                ?>
                            </td>
                            <td class="col-lg-1">
                                <a href="{{url('clerk_courts') . '/' . $clerk_court->id}}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-edit"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <div>
            <a href="{{url("clerk_courts/create")}}" class="btn btn-default">جديد</a>
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