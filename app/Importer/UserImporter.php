<?php

namespace App\Importer;

use App\Models\Department;
use App\Models\User;
use App\Notifications\WelcomeNotification;

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

        // Check if all required data is provided
        if (($this->findCsvMatch($row, 'first_name'))
            && ($this->findCsvMatch($row, 'last_name'))
            && ($this->findCsvMatch($row, 'email'))
        ) {

            // Pull the records from the CSV to determine their values
            $this->item['first_name'] = $this->findCsvMatch($row, 'first_name');
            $this->item['last_name'] = $this->findCsvMatch($row, 'last_name');
            $this->item['email'] = $this->findCsvMatch($row, 'email');
            $this->item['jobtitle'] = $this->findCsvMatch($row, 'jobtitle');
            $this->item['phone'] = $this->findCsvMatch($row, 'phone_number');
            $this->item['notes'] = $this->findCsvMatch($row, 'notes');
            $this->item['activated'] =  (int)filter_var($this->findCsvMatch($row, 'activated'), FILTER_VALIDATE_BOOLEAN);

            // Get username from email address
            $this->item['username'] = $this->item['email'] ? explode('@', $this->item['email'])[0] : null;



            // Handle manager
            $user_manager = [];
            if ($user_manager_email = $this->findCsvMatch($row, 'manager_email')) {
                $user_manager += ['email' => $user_manager_email];
            }
            if (
                ($user_manager_first_name = $this->findCsvMatch($row, 'manager_first_name'))
                && ($user_manager_last_name = $this->findCsvMatch($row, 'manager_last_name'))
            ) {
                $user_manager += [
                    'first_name' => $user_manager_first_name,
                    'last_name' => $user_manager_last_name
                ];
            }
            $this->item['manager_id'] = $this->createOrFetchUser($user_manager);



            // Handle location
            if ($user_location = $this->findCsvMatch($row, 'location')) {
                // Location parent
                $user_location_parent_id = $this->createOrFetchLocation($this->findCsvMatch($row, 'location_parent'));

                // Location manager
                $user_location_manager = [];
                if ($user_location_manager_email = $this->findCsvMatch($row, 'location_manager_email')) {
                    $user_location_manager += ['email' => $user_location_manager_email];
                }
                if (
                    ($user_location_manager_first_name = $this->findCsvMatch($row, 'location_manager_first_name'))
                    && ($user_location_manager_last_name = $this->findCsvMatch($row, 'location_manager_last_name'))
                ) {
                    $user_location_manager += [
                        'first_name' => $user_location_manager_first_name,
                        'last_name' => $user_location_manager_last_name
                    ];
                }
                $user_location_manager_id = $this->createOrFetchUser($user_location_manager);

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

                // Department manager
                $user_department_manager = [];
                if ($user_department_manager_email = $this->findCsvMatch($row, 'department_manager_email')) {
                    $user_department_manager += ['email' => $user_department_manager_email];
                }
                if (
                    ($user_department_manager_first_name = $this->findCsvMatch($row, 'department_manager_first_name'))
                    && ($user_department_manager_last_name = $this->findCsvMatch($row, 'department_manager_last_name'))
                ) {
                    $user_department_manager += [
                        'first_name' => $user_department_manager_first_name,
                        'last_name' => $user_department_manager_last_name
                    ];
                }
                $user_department_manager_id = $this->createOrFetchUser($user_department_manager);

                // Department location
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
            $user = User::where('username', $this->item['username'])
                ->orWhere(function ($query) {
                    $query->where('first_name', $this->item['first_name'])
                        ->where('last_name', $this->item['last_name']);
                })->first();
            if ($user) {
                $this->log('Updating User');
                $user->update($this->item);
                $this->log("User " . $user->username . " was updated");
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
        } else {
            $this->logError(
                "User " . $this->findCsvMatch($row, 'first_name') . " " . $this->findCsvMatch($row, 'first_name'),
                "Some required data is missing"
            );
        }

        return;
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
