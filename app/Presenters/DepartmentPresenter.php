<?php

namespace App\Presenters;

/**
 * Class DepartmentPresenter
 * @package App\Presenters
 */
class DepartmentPresenter extends Presenter
{

    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                "field" => "checkbox",
                "checkbox" => true,
            ],
            [
                "field" => "id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.id'),
                "visible" => false
            ],
            [
                "field" => "company",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.company'),
                "visible" => false,
                "formatter" => "companiesLinkObjFormatter"
            ],
            [
                "field" => "name",
                "searchable" => false,
                "sortable" => true,
                "title" => trans('admin/departments/table.name'),
                "visible" => true,
                "formatter" => "departmentsLinkFormatter"
            ],
            [
                "field" => "image",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.image'),
                "visible" => false,
                "formatter" => "imageFormatter"
            ],
            [
                "field" => "manager",
                "searchable" => false,
                "sortable" => true,
                "title" => trans('admin/departments/table.manager'),
                "visible" => true,
                'formatter' => 'usersLinkObjFormatter'
            ],
            [
                "field" => "users_count",
                "searchable" => false,
                "sortable" => true,
                "title" =>  trans('general.users'),
                "visible" => true,
            ],
            [
                "field" => "location",
                "searchable" => false,
                "sortable" => true,
                "title" =>  trans('admin/departments/table.location'),
                "visible" => true,
                "formatter" => 'locationsLinkObjFormatter'
            ],
            [
                "field" => "actions",
                "searchable" => false,
                "sortable" => false,
                "title" =>  trans('table.actions'),
                "visible" => true,
                "formatter" => 'departmentsActionsFormatter'
            ],
        ];

        return json_encode($layout);
    }



    /**
     * Link to this locations name
     * @return string
     */
    public function nameUrl()
    {
        return (string)link_to_route('departments.show', $this->name, $this->id);
    }

    /**
     * Getter for Polymorphism.
     * @return mixed
     */
    public function name()
    {
        return $this->model->name;
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('departments.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fa fa-map-marker" aria-hidden="true"></i>';
    }

    public function fullName()
    {
        return $this->name;
    }
}
