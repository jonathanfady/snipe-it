<?php

namespace App\Importer;

use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use App\Notifications\WelcomeNotification;

/**
 * This is ONLY used for the User Import. When we are importing users
 * via an Asset/etc import, we use createOrFetchUser() in
 * App\Importer.php. [ALG]
 *
 * Class UserImporter
 * @package App\Importer
 *
 */
class UserImporter extends ItemImporter
{
    protected $send_welcome = false;

    public function __construct($filename)
    {
        parent::__construct($filename);
    }

    protected function handle($row)
    {
        parent::handle($row);

        // Need to reset this between iterations or we'll have stale data.
        $this->item = [];

        // Pull the records from the CSV to determine their values
        $this->item['first_name'] = $this->findCsvMatch($row, 'first_name');
        $this->item['last_name'] = $this->findCsvMatch($row, 'last_name');
        $this->item['email'] = $this->findCsvMatch($row, 'email');
        $this->item['jobtitle'] = $this->findCsvMatch($row, 'jobtitle');
        $this->item['phone'] = $this->findCsvMatch($row, 'phone_number');
        $this->item['notes'] = $this->findCsvMatch($row, 'notes');
        $this->item['activated'] =  (int)filter_var($this->findCsvMatch($row, 'activated'), FILTER_VALIDATE_BOOLEAN));

        // Get username from email address
        $this->item['username'] = $this->item['email'] ? explode('@', $this->item['email'])[0] : null;



        // Handle manager
        if ($user_manager_email = $this->findCsvMatch($row, 'manager_email')) {
            $this->item['manager_id'] = $this->createOrFetchUser([
                'email' => $user_manager_email,
            ]);
        } else if (
            $user_manager_first_name = $this->findCsvMatch($row, 'manager_first_name')
            && $user_manager_last_name = $this->findCsvMatch($row, 'manager_last_name')
        ) {
            $this->item['manager_id'] = $this->createOrFetchUser([
                'first_name' => $user_manager_first_name,
                'last_name' => $user_manager_last_name,
            ]);
        } else {
            $this->item['manager_id'] = null;
        }



        // Handle location
        if ($user_location = $this->findCsvMatch($row, 'location')) {
            $user_location_parent_id = $this->createOrFetchLocation($this->findCsvMatch($row, 'location_parent'));

            if ($user_location_manager_email = $this->findCsvMatch($row, 'location_manager_email')) {
                $user_location_manager_id = $this->createOrFetchUser([
                    'email' => $user_location_manager_email,
                ]);
            } else if (
                $user_location_manager_first_name = $this->findCsvMatch($row, 'location_manager_first_name')
                && $user_location_manager_last_name = $this->findCsvMatch($row, 'location_manager_last_name')
            ) {
                $user_location_manager_id = $this->createOrFetchUser([
                    'first_name' => $user_location_manager_first_name,
                    'last_name' => $user_location_manager_last_name,
                ]);
            } else {
                $user_location_manager_id = null;
            }

            $this->item["location_id"] = $this->createOrFetchLocation(
                $user_location,
                [
                    'parent_id' => $user_location_parent_id,
                    'manager_id' => $user_location_manager_id,
                ]
            );
        } else {
            $this->item['location_id'] = null;
        }



        // Handle department
        if ($user_department = $this->findCsvMatch($row, 'department')) {
            if ($user_department_manager_email = $this->findCsvMatch($row, 'department_manager_email')) {
                $user_department_manager_id = $this->createOrFetchUser([
                    'email' => $user_department_manager_email,
                ]);
            } else if (
                $user_department_manager_first_name = $this->findCsvMatch($row, 'department_manager_first_name')
                && $user_department_manager_last_name = $this->findCsvMatch($row, 'department_manager_last_name')
            ) {
                $user_department_manager_id = $this->createOrFetchUser([
                    'first_name' => $user_department_manager_first_name,
                    'last_name' => $user_department_manager_last_name,
                ]);
            } else {
                $user_department_manager_id = null;
            }

            $user_department_location_id = $this->createOrFetchLocation($this->findCsvMatch($row, 'department_location'));

            $this->item["department_id"] = $this->createOrFetchDepartment(
                [
                    'name' => $user_department,
                    'manager_id' => $user_department_manager_id,
                ],
                [
                    'location_id' => $user_department_location_id,
                ]
            );
        } else {
            $this->item['department_id'] = null;
        }



        // Update or create user
        $user = User::where('username', $this->item['username'])->first();
        if ($user) {
            $this->log('Updating User');
            $user->update($this->item);
        } else {
            $this->log("No matching user, creating one");
            $user = User::create($this->item);
            $this->log("User " . $user->username . " was created");
            if (($user->email) && ($user->activated == '1')) {
                // add default password
                $user->update(['pasword' => bcrypt($this->tempPassword)]);
                $this->log("User " . $user->username . " " . $this->tempPassword . " activated");

                if ($this->send_welcome) {
                    $data = [
                        'email' => $user->email,
                        'username' => $user->username,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'password' => $this->tempPassword,
                    ];
                    $user->notify(new WelcomeNotification($data));
                }
            }
        }

        return;
    }

    /**
     * Fetch an existing location, or create new if it doesn't exist
     *
     * @author Daniel Melzter
     * @since 5.0
     * @param string $location_name 
     * @param array $columns optional columns to update
     * @return int id of location created/found
     */
    public function createOrFetchLocation($location_name, $columns = [])
    {
        return Location::updateOrCreate(['name' => $location_name], $columns)->id;
    }

    /**
     * Fetch an existing department, or create new if it doesn't exist
     *
     * @author Daniel Melzter
     * @since 5.0
     * @param array $department department name value pairs
     * @param array $columns optional columns to update
     * @return int id of department created/found
     */
    public function createOrFetchDepartment($department, $columns)
    {
        return Department::updateOrCreate($department, $columns)->id;
    }
}
