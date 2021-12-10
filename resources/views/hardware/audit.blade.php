@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.audit') }}
@parent
@stop

{{-- Page content --}}
@section('content')

<style>
    .input-group {
        padding-left: 0px !important;
    }
</style>

<div class="row">
    <!-- left column -->
    <div class="col-md-7">
        <div class="box box-default">

            {{ Form::open([
            'method' => 'POST',
            'route' => ['asset.audit.store', $item->id],
            'files' => true,
            'class' => 'form-horizontal' ]) }}

            <div class="box-header with-border">
                <h2 class="box-title"> {{ trans('admin/hardware/form.tag') }} {{ $item->asset_tag }}</h2>
            </div>
            <div class="box-body">
                {{csrf_field()}}
                @if ($item->model)
                <!-- Asset Model Name -->
                <div class="form-group" style="margin-bottom: 0px">
                    {{ Form::label('name', trans('admin/hardware/form.model'), array('class' => 'col-md-3
                    control-label')) }}
                    <div class="col-md-8">
                        <p class="form-control-static">{{ $item->model->name }}</p>
                    </div>
                </div>
                @endif
                @if ($item->model && $item->model->category)
                <!-- Asset Model Category -->
                <div class="form-group" style="margin-bottom: 0px">
                    {{ Form::label('name', trans('general.category'), array('class' => 'col-md-3
                    control-label')) }}
                    <div class="col-md-8">
                        <p class="form-control-static">{{ $item->model->category->name }}</p>
                    </div>
                </div>
                @endif
                @if ($item->model && $item->model->manufacturer)
                <!-- Asset Model Manufacturer -->
                <div class="form-group">
                    {{ Form::label('name', trans('general.manufacturer'), array('class' => 'col-md-3
                    control-label')) }}
                    <div class="col-md-8">
                        <p class="form-control-static">{{ $item->model->manufacturer->name }}</p>
                    </div>
                </div>
                @endif

                <!-- Focal Point -->
                @include ('partials.forms.edit.user-select', ['fieldname' => 'focal_point_id',
                'translated_name' => trans('admin/hardware/form.focal_point'), 'activated_users_only' => 'true'])

                <!-- Status -->
                @include ('partials.forms.edit.status')

                <!-- Checkout to -->
                @include ('partials.forms.checkout-selector', ['user_select' => 'true',
                'location_select' => 'true'])

                @include ('partials.forms.edit.checkout-user')
                @include ('partials.forms.edit.checkout-location')

                <!-- Location -->
                @include ('partials.forms.edit.location-select', ['fieldname' => 'location_id',
                'translated_name' => trans('general.location')])

                <!-- Next Audit -->
                @if (isset($settings))
                @php
                $next_audit_date = Carbon::now()->addMonths($settings->audit_interval)->toDateString();
                @endphp
                @endif
                <div class="form-group {{ $errors->has('next_audit_date') ? 'error' : '' }}">
                    {{ Form::label('name', trans('general.next_audit_date'), array('class' => 'col-md-3 control-label'))
                    }}
                    <div class="col-md-9">
                        <div class="input-group date col-md-5" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control" placeholder="{{ trans('general.next_audit_date') }}"
                                name="next_audit_date" id="next_audit_date"
                                value="{{ old('next_audit_date', $next_audit_date) }}">
                            <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        </div>
                        {!! $errors->first('next_audit_date', '<span class="alert-msg" aria-hidden="true"><i
                                class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
                    </div>
                </div>


                <!-- Notes -->
                <div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
                    <label for="notes" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
                    <div class="col-md-7">
                        <textarea class="col-md-6 form-control" id="notes"
                            name="notes">{{ old('notes', $item->notes) }}</textarea>
                        {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times"
                                aria-hidden="true"></i> :message</span>') !!}
                    </div>
                </div>


                <!-- Images -->
                {{-- @include ('partials.forms.edit.image-upload') --}}

            </div>
            <!--/.box-body-->
            <div class="box-footer">
                <a class="btn btn-link" href="{{ URL::previous() }}"> {{ trans('button.cancel') }}</a>
                <button type="submit" class="btn btn-success pull-right"><i class="fa fa-check icon-white"
                        aria-hidden="true"></i> {{ trans('general.audit') }}</button>
            </div>
            </form>
        </div>
    </div>
    <!--/.col-md-7-->
</div>
@stop