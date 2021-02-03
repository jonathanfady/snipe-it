<!-- Serial -->
<div class="form-group {{ $errors->has('serial') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">{{ trans('admin/hardware/form.serial') }} </label>
    <div class="col-md-7 col-sm-12 @if(\App\Helpers\Helper::checkIfRequired($item, 'serial')) required @endif">
        <input class="form-control" type="text" name="{{ $fieldname }}" id="{{ $fieldname }}"
            value="{{ old('serials') ? old('serials')[1] : $item->serial }}"
            @if(\App\Helpers\Helper::checkIfRequired($item, 'serial' )) data-validation='required' @endif />
        {!! $errors->first('serial', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times"
                aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
