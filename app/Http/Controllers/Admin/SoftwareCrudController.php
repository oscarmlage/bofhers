<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SoftwareRequest as StoreRequest;
use App\Http\Requests\SoftwareRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class SoftwareCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SoftwareCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Software');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/software');
        $this->crud->setEntityNameStrings('software', 'software');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        /* Fields */
        $this->crud->addField([
            'name' => 'name',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'slug',
            'tab' => 'Basic data',
            'hint' => 'Will be automatically generated from name if left empty',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'link',
            'tab' => 'Basic data',
        ]);
        $this->crud->addField([
            'name' => 'description',
            'tab' => 'Basic data',
            'type' => 'simplemde',
        ]);
        $this->crud->addField([
            'name' => 'os',
            'tab' => 'Basic data',
            'type' => 'enum',
        ]);

        /* Columns */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Name',
        ]);
        $this->crud->addColumn([
            'name' => 'description',
            'label' => 'Description',
        ]);
        $this->crud->addColumn([
            'name' => 'os',
            'label' => 'Operating System',
        ]);


        // add asterisk for fields that are required in SoftwareRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
