<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\JobRequest as StoreRequest;
use App\Http\Requests\JobRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class JobCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class JobCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Job');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/job');
        $this->crud->setEntityNameStrings('job', 'jobs');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        // $this->crud->setFromDb();

        /* Fields */
        $this->crud->addField([
            'name' => 'company',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'company_link',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'description',
            'tab' => 'Basic data',
            'type' => 'simplemde',
        ]);
        $this->crud->addField([
            'name' => 'offer_link',
            'tab' => 'Basic data',
        ]);
        $this->crud->addField([
            'name' => 'date',
            'tab' => 'Basic data',
            'type' => 'date',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);
        $this->crud->addField([
            'name' => 'city',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);
        $this->crud->addField([
            'name' => 'is_remote',
            'tab' => 'Basic data',
            'type' => 'radio',
            'options' => [
                0 => "No",
                1 => "Yes"
            ],
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        /* Columns */
        $this->crud->addColumn([
            'name' => 'date',
            'label' => 'Date',
        ]);
        $this->crud->addColumn([
            'name' => 'company',
            'label' => 'Company',
        ]);
        $this->crud->addColumn([
            'name' => 'description',
            'label' => 'Description',
        ]);
        $this->crud->addColumn([
            'name' => 'city',
            'label' => 'City',
        ]);
        $this->crud->addColumn([
            'name' => 'is_remote',
            'label' => 'Remote Job?',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->is_remote ? '<span class="label label-success"><i class="fa fa-check" title="Remote"></i></span>' : '<span class="label label-danger"><i class="fa fa-remove" title="NOT remote"></i></span>';
            }
        ]);


        // add asterisk for fields that are required in JobRequest
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
