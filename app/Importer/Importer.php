<?php

namespace App\Importer;

use ForceUTF8\Encoding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

abstract class Importer
{
    protected $csv;

    /**
     * Id of User performing import
     * @var
     */
    protected $user_id;

    /**
     * Temporary password for activated new users
     * @var
     */
    protected $tempPassword;

    /**
     * Are we updating items in the import
     * @var bool
     */
    // protected $updating;
    /**
     * Default Map of item fields->csv names
     * @var array
     */
    // private $defaultFieldMap = [
    //     'asset_tag' => 'asset tag',
    //     'activated' => 'activated',
    //     'category' => 'category',
    //     'checkout_class' => 'checkout type', // Supports Location or User for assets.  Using checkout_class instead of checkout_type because type exists on asset already.
    //     'checkout_user' => 'checkout user',
    //     'checkout_location' => 'checkout location',
    //     'company' => 'company',
    //     'item_name' => 'item name',
    //     'item_number' => "item number",
    //     'image' => 'image',
    //     'expiration_date' => 'expiration date',
    //     'location' => 'location',
    //     'notes' => 'notes',
    //     'license_email' => 'licensed to email',
    //     'license_name' => "licensed to name",
    //     'maintained' => 'maintained',
    //     'manufacturer' => 'manufacturer',
    //     'asset_model' => "model name",
    //     'model_number' => 'model number',
    //     'order_number' => 'order number',
    //     'purchase_cost' => 'purchase cost',
    //     'purchase_date' => 'purchase date',
    //     'purchase_order' => 'purchase order',
    //     'qty' => 'quantity',
    //     'reassignable' => 'reassignable',
    //     'requestable' => 'requestable',
    //     'seats' => 'seats',
    //     'serial_number' => 'serial number',
    //     'status' => 'status',
    //     'supplier' => 'supplier',
    //     'termination_date' => 'termination date',
    //     'warranty_months' => 'warranty',
    //     'full_name' => 'full name',
    //     'email' => 'email',
    //     'username' => 'username',
    //     'address' => 'address',
    //     'city' => 'city',
    //     'state' => 'state',
    //     'country' => 'country',
    //     'jobtitle' => 'job title',
    //     'employee_num' => 'employee number',
    //     'phone_number' => 'phone number',
    //     'first_name' => 'first name',
    //     'last_name' => 'last name',
    //     'department' => 'department',
    //     'manager_first_name' => 'manager first name',
    //     'manager_last_name' => 'manager last name',
    //     'current_company' => 'current company',
    //     'last_audit_date' => 'last audit date',
    //     'focal_point' => 'focal point',
    // ];
    /**
     * Map of item fields->csv names
     * @var array
     */
    protected $fieldMap = [];

    /**
     * @var callable
     */
    protected $logCallback;
    /**
     * @var callable
     */
    protected $progressCallback;
    /**
     * @var callable
     */
    protected $errorCallback;

    /**
     * @var null
     */
    // protected $usernameFormat;




    /**
     * ObjectImporter constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        // $this->fieldMap = $this->defaultFieldMap;
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }
        // By default the importer passes a url to the file.
        // However, for testing we also support passing a string directly
        if (is_file($file)) {
            $this->csv = Reader::createFromPath($file);
        } else {
            $this->csv = Reader::createFromString($file);
        }
        // $this->tempPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
        $this->tempPassword = "pingpong3000";
    }
    // Cached Values for import lookups
    // protected $customFields;

    /**
     * Sets up the database transaction and logging for the importer
     *
     * @return void
     * @author Daniel Meltzer
     * @since  5.0
     */
    public function import()
    {
        $headerRow = $this->csv->fetchOne();
        $this->csv->setHeaderOffset(0); //explicitly sets the CSV document header record
        $results = $this->normalizeInputArray($this->csv->getRecords($headerRow));

        // $this->populateCustomFields($headerRow);

        DB::transaction(function () use (&$results) {
            Model::unguard();
            // $resultsCount = sizeof($results);
            foreach ($results as $row) {
                $this->log('------------- Action Summary ----------------');
                $success = $this->handle($row);
                if ($this->progressCallback) {
                    call_user_func($this->progressCallback, $success);
                }
            }
        });
    }

    abstract protected function handle($row);

    /**
     * Check to see if the given key exists in the array, and trim excess white space before returning it
     *
     * @author Daniel Melzter
     * @since 3.0
     * @param $array array
     * @param $key string
     * @return string
     */
    public function findCsvMatch(array $array, $key)
    {
        $val = null;
        if (array_key_exists($key, $this->fieldMap)) {
            $key = $this->fieldMap[$key];
        }

        if (array_key_exists($key, $array)) {
            $val = Encoding::toUTF8(trim($array[$key]));
        }
        return $val;
    }

    /**
     * Looks up A custom key in the custom field map
     *
     * @author Daniel Melzter
     * @since 4.0
     * @param $key string
     * @return string|null
     */
    // public function lookupCustomKey($key)
    // {
    //     if (array_key_exists($key, $this->fieldMap)) {
    //         return $this->fieldMap[$key];
    //     }
    //     // Otherwise no custom key, return original.
    //     return $key;
    // }

    /**
     * Used to lowercase header values to ensure we're comparing values properly.
     *
     * @param $results
     * @return array
     */
    public function normalizeInputArray($results)
    {
        $newArray = [];
        foreach ($results as $index => $arrayToNormalize) {
            $newArray[$index] = $arrayToNormalize;
        }
        return $newArray;
    }

    protected function log($string)
    {
        if ($this->logCallback) {
            call_user_func($this->logCallback, $string);
        }
    }

    protected function logError($name, $errorString)
    {
        if ($this->errorCallback) {
            call_user_func($this->errorCallback, $name, $errorString);
        }
    }

    /**
     * Sets the Id of User performing import.
     *
     * @param mixed $user_id the user id
     *
     * @return self
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Sets the Are we updating items in the import.
     *
     * @param bool $updating the updating
     *
     * @return self
     */
    // public function setUpdating($updating)
    // {
    //     $this->updating = $updating;

    //     return $this;
    // }

    /**
     * Sets whether or not we should notify the user with a welcome email
     *
     * @param bool $send_welcome the send-welcome flag
     *
     * @return self
     */
    // public function setShouldNotify($send_welcome)
    // {
    //     $this->send_welcome = $send_welcome;

    //     return $this;
    // }

    /**
     * Defines mappings of csv fields
     *
     * @param bool $updating the updating
     *
     * @return self
     */
    public function setFieldMappings($fields)
    {
        // Some initial sanitization.
        // $fields = array_map('strtolower', $fields);
        // $this->fieldMap = array_merge($this->defaultFieldMap, $fields);
        $this->fieldMap = $fields;

        // $this->log($this->fieldMap);
        return $this;
    }

    /**
     * Sets the callbacks for the import
     *
     * @param callable $logCallback Function to call when we have data to log
     * @param callable $progressCallback Function to call to display progress
     * @param callable $errorCallback Function to call when we have errors
     *
     * @return self
     */
    public function setCallbacks(callable $logCallback, callable $progressCallback, callable $errorCallback)
    {
        $this->logCallback = $logCallback;
        $this->progressCallback = $progressCallback;
        $this->errorCallback = $errorCallback;

        return $this;
    }
}
