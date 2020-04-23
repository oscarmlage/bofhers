<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TelegramRequest as StoreRequest;
use App\Http\Requests\TelegramRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class TelegramCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TelegramCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Telegram');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/telegram');
        $this->crud->setEntityNameStrings('telegram', 'telegrams');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns

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
            'name' => 'text',
            'tab' => 'Basic data',
            'type' => 'simplemde',
        ]);
        $this->crud->addField([
            'name' => 'request',
            'tab' => 'Basic data',
            'type' => 'simplemde',
        ]);
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
            'name' => 'text',
            'type' => 'text',
            'limit' => 40,
        ]);
        $this->crud->addColumn(['name' => 'created_at', 'label' => 'created_at' ] );


        // add asterisk for fields that are required in TelegramRequest
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
