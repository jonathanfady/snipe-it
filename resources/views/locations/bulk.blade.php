@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/locations/form.bulk_update') }}
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

        <p>{{ trans('admin/locations/form.bulk_update_help') }}</p>

        <div class="callout callout-warning">
            <i class="fa fa-warning"></i>
            {{ trans('admin/locations/form.bulk_update_warn', ['location_count' => count($locations)]) }}
        </div>

        <form class="form-horizontal" method="post" action="{{ route('locations/bulksave') }}" autocomplete="off"
            role="form">
            {{ csrf_field() }}

            <div class="box box-default">
                <div class="box-body">

                    <!-- Manager-->
                    @include ('partials.forms.edit.user-select', ['fieldname' => 'manager_id',
                    'translated_name' => trans('admin/users/table.manager'), 'activated_users_only' => 'true'])

                    <!-- Currency -->
                    <div class="form-group {{ $errors->has('currency') ? ' has-error' : '' }}">
                        <label for="currency" class="col-md-3 control-label">
                            {{ trans('admin/locations/table.currency') }}
                        </label>
                        <div class="input-group col-md-2">
                            <input type="text" class="form-control" maxlength="10"
                                placeholder="{{ trans('admin/hardware/form.currency') }}" name="currency" id="currency"
                                value="{{ old('currency') }}">
                            {!! $errors->first('currency', '<span class="alert-msg" aria-hidden="true"><i
                                    class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                        {{ Form::label('city', trans('general.city'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-7">
                            {{Form::text('city', old('city'), array('class' => 'form-control',
                            'aria-label'=>'city')) }}
                            {!! $errors->first('city', '<span class="alert-msg" aria-hidden="true"><i
                                    class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
                        {{ Form::label('state', trans('general.state'), array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-7">
                            {{Form::text('state', old('state'), array('class' => 'form-control',
                            'aria-label'=>'state')) }}
                            {!! $errors->first('state', '<span class="alert-msg" aria-hidden="true"><i
                                    class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}

                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('country') ? ' has-error' : '' }}">
                        {{ Form::label('country', trans('general.country'), array('class' => 'col-md-3 control-label'))
                        }}
                        <div class="col-md-5">
                            {!! Form::countries('country', old('country'), 'select2') !!}
                            {!! $errors->first('country', '<span class="alert-msg" aria-hidden="true"><i
                                    class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    @foreach ($locations as $key => $value)
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