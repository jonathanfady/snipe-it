<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * This controller handles all actions related to Locations for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class LocationsController extends Controller
{

    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the locations listing, which is generated in getDatatable.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see LocationsController::getDatatable() method that generates the JSON response
     * @since [v1.0]
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Grab all the locations
        $this->authorize('view', Location::class);
        // Show the page
        return view('locations/index');
    }


    /**
     * Returns a form view used to create a new location.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see LocationsController::postCreate() method that validates and stores the data
     * @since [v1.0]
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Location::class);
        return view('locations/edit')
            ->with('item', new Location);
    }


    /**
     * Validates and stores a new location.
     *
     * @todo Check if a Form Request would work better here.
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see LocationsController::getCreate() method that makes the form
     * @since [v1.0]
     * @param ImageUploadRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ImageUploadRequest $request)
    {
        $this->authorize('create', Location::class);
        $location = new Location();
        $location->name             = $request->input('name');
        $location->parent_id        = $request->input('parent_id', null);
        $location->currency         = $request->input('currency', '$');
        $location->address          = $request->input('address');
        $location->address2         = $request->input('address2');
        $location->city             = $request->input('city');
        $location->state            = $request->input('state');
        $location->country          = $request->input('country');
        $location->zip              = $request->input('zip');
        $location->ldap_ou          = $request->input('ldap_ou');
        $location->manager_id       = $request->input('manager_id');
        $location->user_id          = Auth::id();

        $location = $request->handleImages($location);

        if ($location->save()) {
            return redirect()->route("locations.index")->with('success', trans('admin/locations/message.create.success'));
        }
        return redirect()->back()->withInput()->withErrors($location->getErrors());
    }


    /**
     * Makes a form view to edit location information.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see LocationsController::postCreate() method that validates and stores
     * @param int $locationId
     * @since [v1.0]
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($locationId = null)
    {
        $this->authorize('update', Location::class);
        // Check if the location exists
        if (is_null($item = Location::find($locationId))) {
            return redirect()->route('locations.index')->with('error', trans('admin/locations/message.does_not_exist'));
        }


        return view('locations/edit', compact('item'));
    }


    /**
     * Validates and stores updated location data from edit form.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see LocationsController::getEdit() method that makes the form view
     * @param ImageUploadRequest $request
     * @param int $locationId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @since [v1.0]
     */
    public function update(ImageUploadRequest $request, $locationId = null)
    {
        $this->authorize('update', Location::class);
        // Check if the location exists
        if (is_null($location = Location::find($locationId))) {
            return redirect()->route('locations.index')->with('error', trans('admin/locations/message.does_not_exist'));
        }

        // Update the location data
        $location->name         = $request->input('name');
        $location->parent_id    = $request->input('parent_id', null);
        $location->currency     = $request->input('currency', '$');
        $location->address      = $request->input('address');
        $location->address2     = $request->input('address2');
        $location->city         = $request->input('city');
        $location->state        = $request->input('state');
        $location->country      = $request->input('country');
        $location->zip          = $request->input('zip');
        $location->ldap_ou      = $request->input('ldap_ou');
        $location->manager_id   = $request->input('manager_id');

        $location = $request->handleImages($location);


        if ($location->save()) {
            return redirect()->route("locations.index")->with('success', trans('admin/locations/message.update.success'));
        }
        return redirect()->back()->withInput()->withInput()->withErrors($location->getErrors());
    }

    /**
     * Validates and deletes selected location.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $locationId
     * @since [v1.0]
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($locationId)
    {
        $this->authorize('delete', Location::class);
        if (is_null($location = Location::find($locationId))) {
            return redirect()->to(route('locations.index'))->with('error', trans('admin/locations/message.not_found'));
        }

        if ($location->users()->count() > 0) {
            return redirect()->to(route('locations.index'))->with('error', trans('admin/locations/message.assoc_users'));
        } elseif ($location->children()->count() > 0) {
            return redirect()->to(route('locations.index'))->with('error', trans('admin/locations/message.assoc_child_loc'));
        } elseif ($location->assets()->count() > 0) {
            return redirect()->to(route('locations.index'))->with('error', trans('admin/locations/message.assoc_assets'));
        } elseif ($location->assignedassets()->count() > 0) {
            return redirect()->to(route('locations.index'))->with('error', trans('admin/locations/message.assoc_assets'));
        }

        if ($location->image) {
            try {
                Storage::disk('public')->delete('locations/' . $location->image);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }
        $location->delete();
        return redirect()->to(route('locations.index'))->with('success', trans('admin/locations/message.delete.success'));
    }


    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the locations detail page.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $id
     * @since [v1.0]
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id = null)
    {
        $location = Location::find($id);

        if (isset($location->id)) {
            return view('locations/view', compact('location'));
        }

        return redirect()->route('locations.index')->with('error', trans('admin/locations/message.does_not_exist'));
    }

    /**
     * Display the bulk edit page.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @return View
     * @internal param int $assetId
     * @since [v2.0]
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function bulkedit(Request $request)
    {
        $this->authorize('update', Location::class);

        if (!$request->filled('ids')) {
            return redirect()->back()->with('error', 'No locations selected');
        }

        if ($request->filled('bulk_actions')) {
            switch ($request->input('bulk_actions')) {
                case 'edit':
                    return view('locations/bulk')
                        ->with('locations', request('ids'));
            }
        }
        return redirect()->back()->with('error', 'No action selected');
    }

    /**
     * Save bulk edits
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @return Redirect
     * @internal param array $assets
     * @since [v2.0]
     */
    public function bulkupdate(Request $request)
    {
        $this->authorize('update', Location::class);

        \Log::debug($request->input('ids'));

        if (!$request->filled('ids') || count($request->input('ids')) <= 0) {
            return redirect()->route("locations.index")->with('warning', trans('No locations selected, so nothing was updated.'));
        }

        $locations = array_keys($request->input('ids'));

        if (($request->filled('parent_id'))
            || ($request->filled('manager_id'))
            || ($request->filled('currency'))
            || ($request->filled('city'))
            || ($request->filled('state'))
            || ($request->filled('country'))
        ) {

            $this->update_array = [];

            $this->conditionallyAddItem('parent_id')
                ->conditionallyAddItem('manager_id')
                ->conditionallyAddItem('currency')
                ->conditionallyAddItem('city')
                ->conditionallyAddItem('state')
                ->conditionallyAddItem('country');

            foreach ($locations as $locationId) {
                Location::find($locationId)
                    ->update($this->update_array);
            } // endforeach

            return redirect()->route("locations.index")->with('success', trans('admin/locations/message.update.success'));
            // no values given, nothing to update
        }
        return redirect()->route("locations.index")->with('warning', trans('admin/locations/message.update.nothing_updated'));
    }

    /**
     * Array to store update data per item
     * @var Array
     */
    private $update_array;

    /**
     * Adds parameter to update array for an item if it exists in request
     * @param  String $field field name
     * @return BulkAssetsController Model for Chaining
     */
    protected function conditionallyAddItem($field)
    {
        if (request()->filled($field)) {
            $this->update_array[$field] = request()->input($field);
        }
        return $this;
    }
}
