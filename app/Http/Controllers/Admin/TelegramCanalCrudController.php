<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TelegramCanalRequest as StoreRequest;
use App\Http\Requests\TelegramCanalRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Telegram_canalCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TelegramCanalCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\TelegramCanal');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/telegram_canal');
        $this->crud->setEntityNameStrings('telegram_canal', 'telegram_canales');

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
            'name' => 'chat_id',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'description',
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
        $this->crud->addColumn([
            'name' => 'name',
        ]);
        $this->crud->addColumn([
            'name' => 'chat_id',
        ]);
        $this->crud->addColumn([
            'name' => 'active',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->active ? '<span class="text-danger"><i class="fa fa-fw fa-pause"></i> Active</span>' : '<span class="text-info"><i class="fa fa-fw fa-play"></i> Inactive</span>';
            }
        ]);

        // add asterisk for fields that are required in Telegram_canalRequest
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
