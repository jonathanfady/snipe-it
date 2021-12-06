{{-- See snipeit_modals.js for what powers this --}}
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h2 class="modal-title">{{ trans('admin/locations/table.create') }}</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('api.locations.store') }}" onsubmit="return false">
                <div class="alert alert-danger" id="modal_error_msg" style="display:none">
                </div>
                <div class="dynamic-form-row">
                    <div class="col-md-4 col-xs-12"><label for="modal-name">{{ trans('general.name') }}:
                        </label></div>
                    <div
                        class="col-md-8 col-xs-12 @if(\App\Helpers\Helper::checkIfRequired(\App\Models\Location::class, 'name')) required @endif">
                        <input type='text' name="name" id='modal-name' class="form-control"></div>
                </div>

                <div class="dynamic-form-row">
                    <div class="col-md-4 col-xs-12"><label for="modal-city">{{ trans('general.city') }}:</label></div>
                    <div class="col-md-8 col-xs-12"><input type='text' name="city" id='modal-city' class="form-control">
                    </div>
                </div>

                <div class="dynamic-form-row">
                    <div class="col-md-4 col-xs-12 country"><label for="modal-country">{{ trans('general.country')
                            }}:</label></div>
                    <div class="col-md-8 col-xs-12">{!! Form::countries('country', Request::old('country'), 'select2
                        country',"modal-country") !!}</div>
                </div>

                <div class="dynamic-form-row">
                    <div class="col-md-4 col-xs-12"><label for="modal-parent">{{
                            trans('admin/locations/table.parent')}}:</label></div>
                    <div
                        class="col-md-7 col-xs-12 @if(\App\Helpers\Helper::checkIfRequired(\App\Models\Location::class, 'parent_id')) required @endif">
                        <select class="js-data-ajax" data-endpoint="locations"
                            data-placeholder="{{ trans('general.select_location') }}" name="parent_id"
                            style="width: 100%" id="modal-parent_id" aria-label="parent_id"></select>
                    </div>
                </div>

                <div class="dynamic-form-row">
                    <div class="col-md-4 col-xs-12"><label for="modal-manager">{{
                            trans('admin/users/table.manager')}}:</label></div>
                    <div
                        class="col-md-7 col-xs-12 @if(\App\Helpers\Helper::checkIfRequired(\App\Models\Location::class, 'manager_id')) required @endif">
                        <select class="js-data-ajax" data-endpoint="users"
                            data-placeholder="{{ trans('general.select_user') }}" name="manager_id" style="width: 100%"
                            id="modal-manager_id" aria-label="manager_id" data-user-activated="1"></select>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('button.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="modal-save">{{ trans('general.save')
                }}</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->