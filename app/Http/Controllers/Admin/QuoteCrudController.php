<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

use Illuminate\Http\Request;
use App\Models\Quote;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\QuoteRequest as StoreRequest;
use App\Http\Requests\QuoteRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class QuoteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class QuoteCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Quote');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/quote');
        $this->crud->setEntityNameStrings('quote', 'quotes');
        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        /* Fields */
        $this->crud->addField([
            'name' => 'nick',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);
        $this->crud->addField([
            'name' => 'first_name',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);
        $this->crud->addField([
            'name' => 'last_name',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);
        $this->crud->addField([
            'name' => 'telegram_user_id',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'chat_id',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'quote',
            'tab' => 'Basic data',
            'type' => 'simplemde',
        ]);
        $this->crud->addField([
            'label' => "Categories",
            'type' => 'select2_multiple',
            'name' => 'categories',
            'entity' => 'categories',
            'attribute' => "name",
            'model' => "App\Models\Category",
            'pivot' => true,
            'wrapperAttributes' => ['class' => 'form-group col-md-12'],
            'tab' => 'Basic data'
        ], 'both');
        $this->crud->addField([
            'name' => 'active',
            'label' => 'Active',
            'type' => 'checkbox',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6']
        ]);

        /* Columns */
        $this->crud->addColumn(['name' => 'id', 'label' => 'id' ] );
        $this->crud->addColumn(['name' => 'chat_id']);
        $this->crud->addColumn(['name' => 'nick']);
        $this->crud->addColumn([
            'name' => 'quote',
            'type' => 'text',
            'limit' => 40,
        ]);
        $this->crud->addColumn([
            'name' => 'active',
            'type' => 'closure',
            'function' => function($entry) {
                if($entry->active == '-1') return '<span class="label label-warning"><i class="fa fa-adjust" title="Already said"></i></span>';
                return $entry->active ? '<span class="label label-success"><i class="fa fa-check" title="Active"></i></span>' : '<span class="label label-danger"><i class="fa fa-remove" title="Inactive"></i></span>';
            }
        ]);

        /* Buttons */
        //$this->crud->limit(500);
        //$this->crud->enableAjaxTable();
        //$this->crud->setDefaultPageLength(10);
        $this->crud->enableBulkActions();
        $this->crud->addBulkDeleteButton();

        // add asterisk for fields that are required in QuoteRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

        $this->crud->addButtonFromView('top', 'change_active', 'change_active', 'end');
        $this->crud->addButtonFromView('bottom', 'change_active', 'change_active', 'beginning');
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

    public function change_active(Request $request) {
        $quotes = Quote::whereIn('id', $request->get('items'))->get();
        foreach ($quotes as $quote) {
            $quote->active = !$quote->active;
            $quote->save();
        }
        return "ok";
    }
}
