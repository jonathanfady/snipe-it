<div id="assignto_selector"
    class="form-group @if($errors->has('checkout_to_type') || $errors->has('assigned_to_user') || $errors->has('assigned_to_location')) has-error @endif"
    @if(!isset($required)) style="display:none" @endif>

    {{ Form::label('checkout_to_type', trans('admin/hardware/form.checkout_to'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-8 @if(isset($required)) required @endif">
        <div class="btn-group" data-toggle="buttons">
            @if ((isset($user_select)) && ($user_select!='false'))
            <label id="checkout_to_type_user" class="btn btn-default @if((isset($item)
            && ($item->assigned_type == 'App\\Models\\User'))) active @endif">
                <input name="checkout_to_type" value="user" aria-label="checkout_to_type" type="radio" @if((isset($item)
                    && ($item->assigned_type ==
                'App\\Models\\User')))checked="checked"@endif><i class="fa fa-user" aria-hidden="true"></i>
                {{ trans('general.user') }}
            </label>
            @endif
            {{-- @if ((isset($asset_select)) && ($asset_select!='false'))
            <label
                class="btn btn-default @if((isset($item) && ($item->assigned_type == 'App\\Models\\Asset'))) active @endif">
                <input name="checkout_to_type" value="asset" aria-label="checkout_to_type" type="radio"
                    @if((isset($item) && ($item->assigned_type == 'App\\Models\\Asset')))checked="checked"@endif><i
                    class="fa fa-barcode" aria-hidden="true"></i> {{ trans('general.asset') }}
            </label>
            @endif --}}
            @if ((isset($location_select)) && ($location_select!='false'))
            <label id="checkout_to_type_location" class="btn btn-default @if((isset($item)
            && ($item->assigned_type == 'App\\Models\\Location'))) active @endif">
                <input name="checkout_to_type" value="location" aria-label="checkout_to_type" type="radio"
                    @if((isset($item) && ($item->assigned_type ==
                'App\\Models\\Location')))checked="checked"@endif><i class="fa fa-map-marker" aria-hidden="true"></i>
                {{ trans('general.location') }}
            </label>
            @endif

        </div>

        {!! $errors->first('checkout_to_type', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times"
                aria-hidden="true"></i> :message</span>') !!}

    </div>

</div>
