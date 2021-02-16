<?php

namespace App\Http\Controllers\Assets;


use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCheckoutRequest;
use App\Http\Traits\AssetCheckoutTrait;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;

class AssetCheckoutController extends Controller
{
    use AssetCheckoutTrait;
    /**
     * Returns a view that presents a form to check an asset out to a
     * user.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param int $assetId
     * @since [v1.0]
     * @return View
     */
    public function create($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find(e($assetId)))) {
            return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.does_not_exist'));
        }

        $this->authorize('checkout', $asset);

        if ($asset->availableForCheckout()) {
            return view('hardware/checkout', compact('asset'));
        }
        return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.checkout.not_available'));
    }

    /**
     * Validate and process the form data to check out an asset to a user.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param AssetCheckoutRequest $request
     * @param int $assetId
     * @return Redirect
     * @since [v1.0]
     */
    public function store(AssetCheckoutRequest $request, $assetId)
    {
        // try {
        // Check if the asset exists
        if (!$asset = Asset::find($assetId)) {
            return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.does_not_exist'));
        } elseif (!$asset->availableForCheckout()) {
            return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.checkout.not_available'));
        }
        $this->authorize('checkout', $asset);
        $admin = Auth::user();

        $checkout_at = date("Y-m-d H:i:s");
        if (($request->filled('checkout_at')) && ($request->get('checkout_at') != date("Y-m-d"))) {
            $checkout_at = $request->get('checkout_at');
        }

        $expected_checkin = '';
        if ($request->filled('expected_checkin')) {
            $expected_checkin = $request->get('expected_checkin');
        }

        $target = $this->determineCheckoutTarget();
        if (isset($target)) {
            $location = $this->determineCheckoutLocation($target);
            if ($asset->checkOut($target, $admin, $checkout_at, $expected_checkin, e($request->get('notes')), $request->get('name'), $location)) {
                return redirect()->route("hardware.index")->with('success', trans('admin/hardware/message.checkout.success'));
            }
        }

        // Redirect to the asset management page with error
        return redirect()->to("hardware/$assetId/checkout")->with('error', trans('admin/hardware/message.checkout.error') . $asset->getErrors());
        // } catch (ModelNotFoundException $e) {
        //     return redirect()->back()->with('error', trans('admin/hardware/message.checkout.error'))->withErrors($asset->getErrors());
        // } catch (CheckoutNotAllowed $e) {
        //     return redirect()->back()->with('error', $e->getMessage());
        // }
    }
}
