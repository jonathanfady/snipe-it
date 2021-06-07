<!-- Currency -->
<div class="form-group {{ $errors->has('currency') ? 'has-error' : '' }}">
    <label for="purchase_cost" class="col-md-3 control-label">{{ trans('admin/hardware/form.currency') }}</label>
    <div class="col-md-9">
        <div class="input-group col-md-2 {{ (\App\Helpers\Helper::checkIfRequired($item, 'currency')) ? "required" : '' }}"
            style="padding-left: 0px;">
            <input class="form-control" type="text" name="currency" aria-label="currency" id="currency"
                value="{{ old('currency', $currency_type) }}" @if(\App\Helpers\Helper::checkIfRequired($item, 'currency'
                )) data-validation="required" required @endif placeholder="{{ $currency_type }}" />
        </div>
        <div class="col-md-9" style="padding-left: 0px;">
            {!! $errors->first('currency', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times"
                    aria-hidden="true"></i> :message</span>') !!}
        </div>
    </div>

</div>
