<?php

namespace App\Http\Requests;

use App\Models\Import;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ItemImportRequest extends FormRequest
{
    protected $progress = ['success' => 0, 'failure' => 0];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'import-type' => 'required',
        ];
    }

    public function import(Import $import)
    {
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        ini_set('memory_limit', '500M');
        $filename = config('app.private_uploads') . '/imports/' . $import->file_path;
        $import->import_type = $this->input('import-type');
        $class = title_case($import->import_type);
        $classString = "App\\Importer\\{$class}Importer";
        $importer = new $classString($filename);
        $import->field_map  = request('column-mappings');
        $import->save();
        // $fieldMappings = [];

        if ($import->field_map) {
            foreach ($import->field_map as $field => $fieldValue) {
                $errorMessage = null;

                if (is_null($fieldValue)) {
                    $errorMessage = trans('validation.import_field_empty');
                    $this->errorCallback("Import " . $import->file_path . " " . $field, $errorMessage);

                    return ['error' => $this->errors, 'progress' => $this->progress];
                }
            }
            // We submit as csv field: column, but the importer is happier if we flip it here.
            // $fieldMappings = array_change_key_case(array_flip($import->field_map));
            // dd($fieldMappings);
        }
        $importer->setCallbacks([$this, 'log'], [$this, 'progress'], [$this, 'errorCallback'])
            ->setUserId(Auth::id())
            // ->setUpdating($this->has('import-update'))
            // ->setShouldNotify($this->has('send-welcome'))
            // ->setUsernameFormat('firstname.lastname')
            ->setFieldMappings(array_flip($import->field_map));
        // $logFile = storage_path('logs/importer.log');
        // \Log::useFiles($logFile);
        $importer->import();
        return ['error' => $this->errors, 'progress' => $this->progress];
    }

    public function log($string)
    {
        Log::Info($string);
    }

    public function progress($success)
    {
        if ($success) {
            $this->progress['success']++;
        } else {
            $this->progress['failure']++;
        }
    }
    public function errorCallback($name, $errorString)
    {
        $this->errors[$name] = $errorString;
    }

    private $errors;
}
