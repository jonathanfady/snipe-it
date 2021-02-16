<?php

namespace App\Http\Traits;

use App\Models\Asset;
use App\Models\Location;
use App\Models\User;

trait AssetCheckoutTrait
{
    /**
     * Find target for checkout
     * @return SnipeModel        Target asset is being checked out to.
     */
    protected function determineCheckoutTarget()
    {
        switch (request('checkout_to_type')) {
            case 'location':
                if (request('assigned_to_location') !== null)
                    return Location::findOrFail(request('assigned_to_location'));
                // case 'asset':
                //     return Asset::findOrFail(request('assigned_asset'));
            case 'user':
                if (request('assigned_to_user') !== null)
                    return User::findOrFail(request('assigned_to_user'));
            default:
                return null;
        }
        return null;
    }

    /**
     * Find location based on target class
     * @return SnipeModel        Target asset is being checked out to.
     */
    protected function determineCheckoutLocation($target)
    {
        switch (get_class($target)) {
            case Location::class:
                return $target->id;
            case User::class:
                return $target->location_id;
            default:
                return null;
        }
    }

    /**
     * Update the location of the asset passed in.
     * @param  Asset $asset Asset being updated
     * @param  SnipeModel $target Target with location
     * @return Asset        Asset being updated
     */
    // protected function updateAssetLocation($asset, $target)
    // {
    //     switch (request('checkout_to_type')) {
    //         case 'location':
    //             $asset->location_id = $target->id;
    //             break;
    //             // case 'asset':
    //             //     $asset->location_id = $target->rtd_location_id;
    //             //     // Override with the asset's location_id if it has one
    //             //     if ($target->location_id != '') {
    //             //         $asset->location_id = $target->location_id;
    //             //     }
    //             //     break;
    //         case 'user':
    //             $asset->location_id = $target->location_id;
    //             break;
    //     }
    //     return $asset;
    // }
}
