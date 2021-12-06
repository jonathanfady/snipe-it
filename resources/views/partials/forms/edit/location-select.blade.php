<div id="{{$fieldname}}" class="form-group {{ $errors->has($fieldname) ? 'has-error' : '' }}" {!! (isset($style))
    ? ' style="' .e($style).'"' : '' !!}>

    {{ Form::label($fieldname, $translated_name, array('class' => 'col-md-3 control-label')) }}
    <div class="col-md-7 @if((isset($required)) && ($required=='true')) required @endif">
        <select class="js-data-ajax" data-endpoint="locations" data-placeholder="{{ trans('general.select_location') }}"
            name="{{$fieldname}}" style="width: 100%" id="{{$fieldname}}_location_select" aria-label="{{$fieldname}}">
            @if ($location_id = old($fieldname, (isset($item)) ? $item->{$fieldname} : ''))
            <option value="{{ $location_id }}" selected="selected" role="option" aria-selected="true" role="option">
                {{ (\App\Models\Location::find($location_id)) ? \App\Models\Location::find($location_id)->name : '' }}
            </option>
            @else
            <option value="" role="option">{{ trans('general.select_location') }}</option>
            @endif
        </select>
    </div>

    <div class="col-md-1 col-sm-1 text-left">
        @can('create', \App\Models\Location::class)
        <a href="{{ route( 'modal.show', 'location' ) }}" data-toggle="modal" data-target="#createModal"
            data-select='{{$fieldname}}_location_select' class="btn btn-sm btn-primary">New</a>
        @endcan
    </div>

    {!! $errors->first($fieldname, '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i
                class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}

</div>