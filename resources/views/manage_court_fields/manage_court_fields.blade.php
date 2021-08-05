@extends('layouts.app')

@section('content')
<div class="box">
    {!! Form::open(['url' => 'manage_court_fields', 'method' => 'post']) !!}
    <div class="box-body">
        <div class="row form-group">
            <div class="col-lg-1 text-left">
                المحكمة
            </div>
            <div class="col-lg-3">
                {!! Form::select('id', $names, isset($name->id)?$name->id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow', "title" => ' -- Select One -- ', "id" => "refreshConfigForm", "data-uri" => url('manage_court_fields')]) !!}
            </div>
            <div class="col-lg-1 text-left">
                النوع
            </div>
            <div class="col-lg-2">
                {!! Form::select('type_id', $types, isset($type->id)?$type->id:'', ['class' => 'form-control selectpicker show-tick show-menu-arrow', "title" => ' -- Select One -- ', "id" => "refreshConfigForm", "data-uri" => url('manage_court_fields')]) !!}
            </div>
        </div>

        <div class="row form-group">
            <div class="clearfix"></div>
            @foreach($separated as $id => $separated_item)
            <div class="col-lg-3">
                <input type='text' value="{{isset($court_fields[$id])?$court_fields[$id]:''}}" name='order[{{$id}}][]' class='form-control' style='width:35px;display: inline-block;padding: 0px;text-align: center;height: 22px;vertical-align: middle;border:1px solid #f4f4f4'/>
                <input type="checkbox" name="separated[]" {{isset($court_fields[$id])?"checked":""}} value="{{$id}}"/> {{$separated_item}}
            </div>
            @endforeach
        </div>
        <hr>
        <div>
            <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> حفظ</button>
        </div>
    </div>
</form>
</div>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('LTE/plugins/iCheck/all.css')}}">
<!-- iCheck 1.0.1 -->
<script src="{{asset('LTE/plugins/iCheck/icheck.min.js')}}"></script>
<script>
    $('input[type="checkbox"]').iCheck({checkboxClass: 'icheckbox_square-blue'});
</script>
@endsection