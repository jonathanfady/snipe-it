@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.locations') }}
@parent
@stop

@section('header_right')
@can('create', \App\Models\Location::class)
<a href="{{ route('locations.create') }}" class="btn btn-primary pull-right">
  {{ trans('general.create') }}</a>
@endcan
@stop
{{-- Page content --}}
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-body">
        {{ Form::open([
        'method' => 'POST',
        'route' => ['locations/bulkedit'],
        'class' => 'form-inline',
        'id' => 'bulkForm']) }}
        <div class="row">
          <div class="col-md-12">
            @if (Request::get('status')!='Deleted')
            <div id="toolbar">
              <label for="bulk_actions"><span class="sr-only">Bulk Actions</span></label>
              <select name="bulk_actions" class="form-control" aria-label="bulk_actions">
                <option value="edit">{{ trans('button.edit') }}</option>
              </select>
              <button class="btn btn-primary" id="bulkEdit">Go</button>
            </div>
            @endif
            <div class="table-responsive">

              <table data-columns="{{ \App\Presenters\LocationPresenter::dataTableLayout() }}"
                data-cookie-id-table="locationTable" data-pagination="true" data-id-table="locationTable"
                data-search="true" data-show-footer="true" data-side-pagination="server" data-show-columns="true"
                data-show-export="true" data-show-refresh="true" data-sort-order="asc" id="locationTable"
                class="table table-striped snipe-table" data-url="{{ route('api.locations.index') }}"
                data-export-options='{
              "fileName": "export-locations-{{ date(' Y-m-d') }}", "ignoreColumn" :
                ["actions","image","change","checkbox","checkincheckout","icon"] }'>
              </table>
            </div>
          </div><!-- /.col -->
        </div><!-- /.row -->
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'locations-export', 'search' => true])

@stop