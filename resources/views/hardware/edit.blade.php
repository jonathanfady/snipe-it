@extends('layouts/edit-form', [
'createText' => trans('admin/hardware/form.create'),
'updateText' => trans('admin/hardware/form.update'),
'topSubmit' => true,
'helpText' => trans('help.assets'),
'helpPosition' => 'right',
'formAction' => ($item->id) ? route('hardware.update', ['hardware' => $item->id]) : route('hardware.store'),
])


{{-- Page content --}}

@section('inputFields')

@include ('partials.forms.edit.company-select', ['translated_name' => trans('general.company'),
'fieldname' => 'company_id'])


<!-- Asset Tag -->
<div class="form-group {{ $errors->has('asset_tag') ? ' has-error' : '' }}">
    <label for="asset_tag" class="col-md-3 control-label">{{ trans('admin/hardware/form.tag') }}</label>

    <!-- we are editing an existing asset -->
    {{-- @if ($item->id) --}}
    <div class="col-md-7 col-sm-12 @if(\App\Helpers\Helper::checkIfRequired($item, 'asset_tag')) required @endif">
        <input class="form-control" type="text" name="asset_tags[1]" id="asset_tag"
            value="{{ old('asset_tags') ? old('asset_tags')[1] : $item->asset_tag }}"
            @if(\App\Helpers\Helper::checkIfRequired($item, 'asset_tag' )) data-validation="required" @endif>
        {!! $errors->first('asset_tags', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
        {!! $errors->first('asset_tag', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
    {{-- @else
    <!-- we are creating a new asset - let people use more than one asset tag -->
    <div class="col-md-7 col-sm-12{{  (\App\Helpers\Helper::checkIfRequired($item, 'asset_tag')) ? ' required' : '' }}">
    <input class="form-control" type="text" name="asset_tags[1]" id="asset_tag"
        value="{{ Request::old('asset_tag', \App\Models\Asset::autoincrement_asset()) }}" data-validation="required">
    {!! $errors->first('asset_tags', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    {!! $errors->first('asset_tag', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
</div>
<div class="col-md-2 col-sm-12">
    <button class="add_field_button btn btn-default btn-sm">
        <i class="fa fa-plus"></i>
    </button>
</div>
@endif --}}
</div>
@include ('partials.forms.edit.serial', ['fieldname'=> 'serials[1]', 'translated_serial' =>
trans('admin/hardware/form.serial')])

{{-- <div class="input_fields_wrap">
</div> --}}


@include ('partials.forms.edit.model-select', ['translated_name' => trans('admin/hardware/form.model'), 'fieldname' =>
'model_id'])


<div id='custom_fields_content'>
    <!-- Custom Fields -->
    @if ($item->model && $item->model->fieldset)
    <?php $model=$item->model; ?>
    @endif
    @if (Request::old('model_id'))
    <?php $model=\App\Models\AssetModel::find(Request::old('model_id')); ?>
    @elseif (isset($selected_model))
    <?php $model=$selected_model; ?>
    @endif
    @if (isset($model) && $model)
    @include("models/custom_fields_form",["model" => $model])
    @endif
</div>

<!-- Focal Point -->
@include ('partials.forms.edit.user-select', ['fieldname' => 'focal_point_id',
'translated_name' => trans('admin/hardware/form.focal_point'), 'activated_users_only' => 'true'])

@include ('partials.forms.edit.status', ['required' => 'true'])

{{-- @if (!$item->id) --}}
@include ('partials.forms.checkout-selector', ['user_select' => 'true', 'location_select' =>
'true', 'style' => 'display:none;'])

@include ('partials.forms.edit.checkout-user')

{{-- @include ('partials.forms.edit.asset-select', ['translated_name' => trans('admin/hardware/form.checkout_to'),
'fieldname' => 'assigned_asset', 'style' => 'display:none;', 'required' => 'false']) --}}

@include ('partials.forms.edit.checkout-location')
{{-- @endif --}}

@include ('partials.forms.edit.name', ['translated_name' => trans('admin/hardware/form.name')])
@include ('partials.forms.edit.purchase_date')
@include ('partials.forms.edit.supplier-select', ['translated_name' => trans('general.supplier'), 'fieldname' =>
'supplier_id'])
@include ('partials.forms.edit.order_number')
<?php
    $currency_type=null;
    if ($item->id && $item->location) {
        $currency_type = $item->location->currency;
    }
    ?>
@include ('partials.forms.edit.purchase_cost', ['currency_type' => $currency_type])
@include ('partials.forms.edit.warranty')
<!-- Notes -->
<div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
    <label for="notes" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
    <div class="col-md-7">
        <textarea class="col-md-6 form-control" id="notes" name="notes">{{ old('notes', $item->notes) }}</textarea>
        {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times"
                aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

<!-- Current Company -->
@include ('partials.forms.edit.company-select', ['translated_name' => trans('admin/hardware/form.current_company'),
'fieldname' => 'current_company_id', 'hide_new' => 'true'])

@include ('partials.forms.edit.location-select', ['translated_name' => trans('admin/hardware/form.default_location'),
'fieldname' => 'rtd_location_id'])


@include ('partials.forms.edit.requestable', ['requestable_text' => trans('admin/hardware/general.requestable')])

<!-- Image -->
@if ($item->image)
<div class="form-group {{ $errors->has('image_delete') ? 'has-error' : '' }}">
    <label class="col-md-3 control-label" for="image_delete">{{ trans('general.image_delete') }}</label>
    <div class="col-md-5">
        <label class="control-label" for="image_delete">
            <input type="checkbox" value="1" name="image_delete" id="image_delete" class="minimal"
                {{ Request::old('image_delete') == '1' ? ' checked="checked"' : '' }}>
            {!! $errors->first('image_delete', '<span class="alert-msg">:message</span>') !!}
        </label>
        <div style="margin-top: 0.5em">
            <img src="{{ Storage::disk('public')->url(app('assets_upload_path').e($item->image)) }}"
                class="img-responsive" />
        </div>
    </div>
</div>
@endif

@include ('partials.forms.edit.image-upload')

@stop
