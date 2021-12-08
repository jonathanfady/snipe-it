<?php

namespace App\Importer;

use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Company;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\Statuslabel;
use App\Models\Supplier;
use App\Models\User;

class ItemImporter extends Importer
{
    protected $item;

    public function __construct($filename)
    {
        parent::__construct($filename);
    }

    protected function handle($row)
    {
        // Need to reset this between iterations or we'll have stale data.
        $this->item = [];
    }



    /**
     * Fetch an existing User, or create it.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param array $user user name value pairs
     * @return int User id
     */
    protected function createOrFetchUser($user)
    {
        if (empty($user)) {
            return null;
        }

        if ((array_key_exists('first_name', $user))
            && (array_key_exists('last_name', $user))
        ) {
            return User::firstOrCreate($user)->id;
        } else if (array_key_exists('email', $user)) {
            // Add First and Last names as temporary email values
            $user['first_name'] = $user['email'];
            $user['last_name'] = $user['email'];

            return User::firstOrCreate($user)->id;
        } else {
            return null;
        }
    }

    /**
     * Fetch an existing Company, or create it.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param string $name
     * @return int Company id
     */
    public function createOrFetchCompany($name)
    {
        return Company::firstOrCreate(['name' => $name])->id;
    }

    /**
     * Fetch an existing Manufacturer, or create it.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param string $name
     * @return int Manufacturer id
     */
    public function createOrFetchManufacturer($name)
    {
        return Manufacturer::firstOrCreate(['name' => $name])->id;
    }

    /**
     * Fetch an existing Category, or create it.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param string $name
     * @return int Category id
     * @internal param string $item_type
     */
    public function createOrFetchCategory($name)
    {
        // Magic to transform "AssetImporter" to "asset" or similar.
        $classname = class_basename(get_class($this));
        $item_type = strtolower(substr($classname, 0, strpos($classname, 'Importer')));

        return Category::firstOrCreate(['name' => $name, 'category_type' => $item_type])->id;
    }

    /**
     * Fetch an existing AssetModel, or create it.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param array $model model name value pairs
     * @return int AssetModel id
     */
    public function createOrFetchModel($model)
    {
        return AssetModel::firstOrCreate($model)->id;
    }

    /**
     * Fetch an existing Supplier, or create it.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param string $name
     * @return int Supplier id
     */
    public function createOrFetchSupplier($name)
    {
        return Supplier::firstOrCreate(['name' => $name])->id;
    }

    /**
     * Fetch an existing Location, or create it.
     *
     * @author Daniel Melzter
     * @since 5.0
     * @param string $name
     * @param array $columns optional columns to update
     * @return int Location id
     */
    public function createOrFetchLocation($name, $columns = [])
    {
        return Location::updateOrCreate(['name' => $name], $columns)->id;
    }

    /**
     * Fetch an existing Statuslabel, or fetch the first Statuslabel.
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param string $name
     * @return int Statuslabel id
     */
    public function fetchStatusLabel($name)
    {
        if ($status = Statuslabel::where(['name' => $name])->first()) {
            return $status->id;
        } else {
            return Statuslabel::first()->id;
        }
    }
}
