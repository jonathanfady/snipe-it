<div id="assigned_to_user" class="form-group {{ $errors->has('assigned_to_user') ? 'has-error' : '' }}"
    style="display:none">

    {{ Form::label('assigned_to', trans('admin/hardware/form.checkout_to'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-6 @if(isset($required)) required @endif">
        <select class="js-data-ajax" data-endpoint="users" data-placeholder="{{ trans('general.select_user') }}"
            name="assigned_to_user" style="width: 100%" id="assigned_to_select" aria-label="assigned_to">
            @if ($id = (isset($item) && ($item->assigned_type == 'App\\Models\\User')) ? old('assigned_to',
            $item->assigned_to) : '')
            <option value="{{ $id }}" selected="selected" role="option" aria-selected="true" role="option">
                {{ (\App\Models\User::find($id)) ? \App\Models\User::find($id)->present()->fullName : '' }}
            </option>
            @else
            <option value="" role="option">{{ trans('general.select_user') }}</option>
            @endif
        </select>
    </div>

    <div class="col-md-1 col-sm-1 text-left">
        @can('create', \App\Models\User::class)
        <a href='{{ route('modal.show', 'user') }}' data-toggle="modal" data-target="#createModal"
            data-select='assigned_to_select' class="btn btn-sm btn-primary">New</a>
        @endcan
    </div>

    {!! $errors->first('assigned_to_user', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg"
            aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}

</div>
