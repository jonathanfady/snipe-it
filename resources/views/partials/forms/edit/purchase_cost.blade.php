<!-- Purchase Cost -->
<div class="form-group {{ $errors->has('purchase_cost') ? ' has-error' : '' }}">
    <label for="purchase_cost" class="col-md-3 control-label">{{ trans('general.purchase_cost') }}</label>
    <div class="col-md-9">
        <div class="input-group col-md-4 {{ (\App\Helpers\Helper::checkIfRequired($item, 'purchase_cost')) ? "required" : '' }}"
            style="padding-left: 0px;">
            <input class="form-control" type="text" name="purchase_cost" aria-label="purchase_cost" id="purchase_cost"
                value="{{ old('purchase_cost', \App\Helpers\Helper::formatCurrencyOutput($item->purchase_cost)) }}"
                @if(\App\Helpers\Helper::checkIfRequired($item, 'purchase_cost' )) data-validation="required" required
                @endif />
            <span class="input-group-addon">
                @if (isset($currency_type))
                {{ $currency_type }}
                @else
                {{ $snipeSettings->default_currency }}
                @endif
            </span>
        </div>
        <div class="col-md-9" style="padding-left: 0px;">
            {!! $errors->first('purchase_cost', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times"
                    aria-hidden="true"></i> :message</span>') !!}
        </div>
    </div>

</div>
