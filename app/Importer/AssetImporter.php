<?php

namespace App\Importer;

use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AssetImporter extends ItemImporter
{
    public function __construct($filename)
    {
        parent::__construct($filename);
    }

    protected function handle($row)
    {
        parent::handle($row);

        // Check if all required data is provided
        if (($this->findCsvMatch($row, 'company'))
            && ($this->findCsvMatch($row, 'model'))
            && ($this->findCsvMatch($row, 'manufacturer'))
            && ($this->findCsvMatch($row, 'category'))
            && ($this->findCsvMatch($row, 'supplier'))
            && ($this->findCsvMatch($row, 'asset_tag'))
            && ($this->findCsvMatch($row, 'serial'))
            && ($this->findCsvMatch($row, 'purchase_cost'))
            && ($this->findCsvMatch($row, 'purchase_date'))
            && ($this->findCsvMatch($row, 'order_number'))
            && (($this->findCsvMatch($row, 'focal_point_email'))
                || (($this->findCsvMatch($row, 'focal_point_first_name'))
                    && ($this->findCsvMatch($row, 'focal_point_last_name'))))
        ) {


            // Pull the records from the CSV to determine their values
            $this->item['asset_tag'] = $this->findCsvMatch($row, 'asset_tag');
            $this->item['serial'] = $this->findCsvMatch($row, 'serial');
            $this->item['name'] = $this->findCsvMatch($row, 'name');
            $this->item['purchase_date'] = $this->findCsvMatch($row, 'purchase_date');
            $this->item['order_number'] = $this->findCsvMatch($row, 'order_number');
            $this->item['purchase_cost'] = $this->findCsvMatch($row, 'purchase_cost');
            $this->item['notes'] = $this->findCsvMatch($row, 'notes');
            $this->item['last_audit_date'] = $this->findCsvMatch($row, 'last_audit_date');

            $this->item['company_id'] = $this->createOrFetchCompany($this->findCsvMatch($row, 'company'));
            $this->item["manufacturer_id"] = $this->createOrFetchManufacturer($this->findCsvMatch($row, 'manufacturer'));
            $this->item["category_id"] = $this->createOrFetchCategory($this->findCsvMatch($row, 'category'));
            $this->item['status_id'] = $this->fetchStatusLabel($this->findCsvMatch($row, 'status'));
            $this->item['supplier_id'] = $this->createOrFetchSupplier($this->findCsvMatch($row, 'supplier'));
            $this->item['current_company_id'] = $this->createOrFetchCompany($this->findCsvMatch($row, 'current_company'));



            // Handle model
            $this->item['model_id'] = $this->createOrFetchModel(
                [
                    'name' => $this->findCsvMatch($row, 'model'),
                    'manufacturer_id' => $this->item["manufacturer_id"],
                    'category_id' => $this->item["category_id"],
                ]
            );



            // Handle focal point
            $asset_focal_point = [];
            if ($asset_focal_point_email = $this->findCsvMatch($row, 'focal_point_email')) {
                $asset_focal_point += ['email' => $asset_focal_point_email];
            }
            if (
                $asset_focal_point_first_name = $this->findCsvMatch($row, 'focal_point_first_name')
                && $asset_focal_point_last_name = $this->findCsvMatch($row, 'focal_point_last_name')
            ) {
                $asset_focal_point += [
                    'first_name' => $asset_focal_point_first_name,
                    'last_name' => $asset_focal_point_last_name
                ];
            }
            $this->item['focal_point_id'] = $this->createOrFetchUser($asset_focal_point);



            // Handle location
            if ($asset_location = $this->findCsvMatch($row, 'location')) {
                // Location parent
                $asset_location_parent_id = $this->createOrFetchLocation($this->findCsvMatch($row, 'location_parent'));

                // Location manager
                $asset_location_manager = [];
                if ($asset_location_manager_email = $this->findCsvMatch($row, 'location_manager_email')) {
                    $asset_location_manager += ['email' => $asset_location_manager_email];
                }
                if (
                    $asset_location_manager_first_name = $this->findCsvMatch($row, 'location_manager_first_name')
                    && $asset_location_manager_last_name = $this->findCsvMatch($row, 'location_manager_last_name')
                ) {
                    $asset_location_manager += [
                        'first_name' => $asset_location_manager_first_name,
                        'last_name' => $asset_location_manager_last_name
                    ];
                }
                $asset_location_manager_id = $this->createOrFetchUser($asset_location_manager);

                $this->item["location_id"] = $this->createOrFetchLocation(
                    $asset_location,
                    [
                        'parent_id' => $asset_location_parent_id,
                        'manager_id' => $asset_location_manager_id,
                    ]
                );
            } else {
                $this->item['location_id'] = null;
            }
            // Set Ready To Deploy location as well
            $this->item['rtd_location_id'] = $this->item['location_id'];

            // Update or create asset
            $asset = Asset::where(['asset_tag' => $this->item['asset_tag']])->first();
            if ($asset) {
                $this->log("Updating Asset");
                $asset->update($this->item);
                $this->log("Asset " . $asset->asset_tag . " with serial number " . $asset->serial . " was updated");
            } else {
                $this->log("No Matching Asset, creating one");
                $asset = Asset::create($this->item);
                $asset->logCreate('Imported using csv file.');
                $this->log("Asset " . $asset->asset_tag . " with serial number " . $asset->serial . " was created");
            }



            // Handle checkout
            if (($this->findCsvMatch($row, 'checkout_user_email'))
                || ($this->findCsvMatch($row, 'checkout_user_first_name')
                    && $this->findCsvMatch($row, 'checkout_user_last_name'))
            ) {
                $asset_checkout_user = [];
                if ($asset_checkout_user_email = $this->findCsvMatch($row, 'checkout_user_email')) {
                    $asset_checkout_user += ['email' => $asset_checkout_user_email];
                }
                if (
                    $asset_checkout_user_first_name = $this->findCsvMatch($row, 'checkout_user_first_name')
                    && $asset_checkout_user_last_name = $this->findCsvMatch($row, 'checkout_user_last_name')
                ) {
                    $asset_checkout_user += [
                        'first_name' => $asset_checkout_user_first_name,
                        'last_name' => $asset_checkout_user_last_name
                    ];
                }
                $target = User::find($this->createOrFetchUser($asset_checkout_user));
            } else if ($asset_checkout_location = $this->findCsvMatch($row, "checkout_location")) {
                $target = Location::find($this->createOrFetchLocation($asset_checkout_location));
            } else {
                $target = null;
            }
            $asset->checkOut($target);
        } else {
            $this->log("Can't handle Asset " . print_r($row, true) . "Some information is missing.");
        }

        return;
    }
}
