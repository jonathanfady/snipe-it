@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/departments/form.bulk_update') }}
@parent
@stop


@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-sm btn-primary pull-right">
    {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">

        <p>{{ trans('admin/departments/form.bulk_update_help') }}</p>

        <div class="callout callout-warning">
            <i class="fa fa-warning"></i>
            {{ trans('admin/departments/form.bulk_update_warn', ['department_count' => count($departments)]) }}
        </div>

        <form class="form-horizontal" method="post" action="{{ route('departments/bulksave') }}" autocomplete="off"
            role="form">
            {{ csrf_field() }}

            <div class="box box-default">
                <div class="box-body">

                    <!-- Manager-->
                    @include ('partials.forms.edit.user-select', ['fieldname' => 'manager_id',
                    'translated_name' => trans('admin/users/table.manager'), 'activated_users_only' => 'true'])

                    <!-- Location -->
                    @include ('partials.forms.edit.location-select', ['translated_name' =>
                    trans('general.location'),
                    'fieldname' => 'location_id'])

                    @foreach ($departments as $key => $value)
                    <input type="hidden" name="ids[{{ $key }}]" value="1">
                    @endforeach
                </div>
                <!--/.box-body-->

                <div class="box-footer text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"
                            aria-hidden="true"></i> {{ trans('general.save') }}</button>
                </div>
            </div>
            <!--/.box.box-default-->
        </form>
    </div>
    <!--/.col-md-8-->
</div>
@stop