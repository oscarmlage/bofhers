<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\BofhRequest as StoreRequest;
use App\Http\Requests\BofhRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class BofhCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class BofhCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Bofh');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/bofh');
        $this->crud->setEntityNameStrings('bofh', 'bofhs');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();
        // email, email_verified, name, nick, password, password2, token, slug

        /* Fields */
        $this->crud->addField([
            'name' => 'name',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'nick',
            'tab' => 'Basic data',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->crud->addField([
            'name' => 'email',
            'type' => 'email',
            'label' => 'Email',
            'tab' => 'Basic data',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6'
            ]
        ]);
        $this->crud->addField([
            'name' => 'slug',
            'tab' => 'Basic data',
            'hint' => 'Will be automatically generated from name if left empty',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        $this->crud->addField([
            'name' => 'contrasena',
            'type' => 'password',
            'label' => 'Password',
            'tab' => 'Basic data',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6'
            ]
        ], 'create');
        $this->crud->addField([
            'name' => 'contrasena_confirmation',
            'type' => 'password',
            'label' => 'Password Again',
            'tab' => 'Basic data',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6'
            ]
        ], 'create');

        $this->crud->addField([
            'name' => 'contrasena',
            'type' => 'password',
            'label' => 'Password <span>(only if you want to change it)</span>',
            'tab' => 'Basic data',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6'
            ]
        ], 'update');
        $this->crud->addField([
            'name' => 'contrasena_confirmation',
            'type' => 'password',
            'label' => 'Password Again <span>(only if you want to change it)</span>',
            'tab' => 'Basic data',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6'
            ]
        ], 'update');

        $this->crud->addField([
            'name' => 'bio',
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
            'wrapperAttributes' => ['class' => 'form-group col-md-8'],
            'tab' => 'Basic data'
        ], 'both');
        $this->crud->addField([
            'name' => 'is_active',
            'label' => 'Is Active?',
            'type' => 'checkbox',
            'tab' => 'Basic data',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4'
            ]
        ]);


        /* Columns */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Name',
        ]);
        $this->crud->addColumn([
            'name' => 'nick',
            'label' => 'Nick',
        ]);
        $this->crud->addColumn([
            'name' => 'bio',
            'label' => 'Bio',
        ]);
        $this->crud->addColumn([
            'name' => 'categories',
            'label' => 'Categories',
            'type' => 'closure',
            'function' => function($entry) {
                $out = '';
                foreach($entry->categories as $cat) {
                    $out .= $cat->name .', ';
                }
                return rtrim($out, ', ');
            }
        ]);

        $this->crud->addColumn([
            'name' => 'is_active',
            'label' => 'Is Active?',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->is_active ? '<span class="label label-success"><i class="fa fa-check" title="Remote"></i></span>' : '<span class="label label-danger"><i class="fa fa-remove" title="NOT remote"></i></span>';
            }
        ]);

        // add asterisk for fields that are required in BofhRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        if($request['contrasena']) {
            $request->merge(['password' => \Hash::make($request->get('contrasena'))]);
        }
        unset($request['contrasena'], $request['contrasena_confirmation']);

        $redirect_location = parent::storeCrud($request);
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        if($request['contrasena']) {
            $request->merge(['password' => \Hash::make($request->get('contrasena'))]);
        }
        unset($request['contrasena'], $request['contrasena_confirmation']);

        $redirect_location = parent::updateCrud($request);
        return $redirect_location;
    }
}
