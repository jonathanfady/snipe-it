<!-- Company -->
@if (($snipeSettings->full_multiple_companies_support=='1') && (!Auth::user()->isSuperUser()))
<!-- full company support is enabled and this user isn't a superadmin -->
<div class="form-group">
    {{ Form::label($fieldname, $translated_name, array('class' => 'col-md-3 control-label')) }}
    <div class="col-md-6 @if(isset($item) && \App\Helpers\Helper::checkIfRequired($item, $fieldname)) required @endif">
        <select class="js-data-ajax" data-endpoint="companies" data-placeholder="{{ trans('general.select_company') }}"
            name="{{ $fieldname }}" style="width: 100%" aria-label="{{ $fieldname }}" @if(isset($item) &&
            \App\Helpers\Helper::checkIfRequired($item, $fieldname)) data-validation='required' @endif>
            @if ($company_id = old($fieldname, (isset($item)) ? $item->{$fieldname} : ''))
            <option value="{{ $company_id }}" selected="selected" role="option" aria-selected="true" role="option">
                {{ (\App\Models\Company::find($company_id)) ? \App\Models\Company::find($company_id)->name : '' }}
            </option>
            @else
            <option value="" role="option">{{ trans('general.select_company') }}</option>
            @endif
        </select>
    </div>

    <div class="col-md-1 col-sm-1 text-left">
        @can('create', \App\Models\Company::class)
        @if ((!isset($hide_new)) || ($hide_new!='true'))
        <a href='{{ route('modal.show', 'company') }}' data-toggle="modal" data-target="#createModal"
            data-select='company_select' class="btn btn-sm btn-primary">New</a>
        @endif
        @endcan
    </div>
</div>

@else
<!-- full company support is enabled or this user is a superadmin -->
<div id="{{ $fieldname }}" class="form-group{{ $errors->has($fieldname) ? ' has-error' : '' }}">
    {{ Form::label($fieldname, $translated_name, array('class' => 'col-md-3 control-label')) }}
    <div class="col-md-6
    @if(isset($item) && \App\Helpers\Helper::checkIfRequired($item, $fieldname)) required @endif">
        <select class=" js-data-ajax" data-endpoint="companies" data-placeholder="{{ trans('general.select_company') }}"
            name="{{ $fieldname }}" style="width: 100%" @if(isset($item) && \App\Helpers\Helper::checkIfRequired($item,
            $fieldname)) data-validation='required' @endif>
            @if ($company_id = Request::old($fieldname, (isset($item)) ? $item->{$fieldname} : ''))
            <option value=" {{ $company_id }}" selected="selected">
                {{ (\App\Models\Company::find($company_id)) ? \App\Models\Company::find($company_id)->name : '' }}
            </option>
            @else
            <option value="">{{ trans('general.select_company') }}</option>
            @endif
        </select>
    </div>

    <div class="col-md-1 col-sm-1 text-left">
        @can('create', \App\Models\Company::class)
        @if ((!isset($hide_new)) || ($hide_new!='true'))
        <a href='{{ route('modal.show', 'company') }}' data-toggle="modal" data-target="#createModal"
            data-select='company_select' class="btn btn-sm btn-primary">New</a>
        @endif
        @endcan
    </div>

    {!! $errors->first($fieldname, '<div class="col-md-8 col-md-offset-3"><span class="alert-msg"><i
                class="fa fa-times"></i> :message</span></div>') !!}

    {!! $errors->first($fieldname, '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i
                class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}
</div>

@endif
