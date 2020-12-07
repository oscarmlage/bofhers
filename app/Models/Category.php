<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Category extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function sluggable() {
        return [
            'slug' => ['source' => 'slug_or_name',],
        ];
    }

    /**
     * Tries to find a category by its name and returns the model object, or
     * null if there's no such category.
     *
     * @param string $category_name the category name
     *
     * @return Category|null
     */
    public static function fromName(string $category_name): ?Category
    {
        return static::where('name', '=', $category_name)->first();
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function users() {
        return $this->belongsToMany(
            'App\User', 'users_categories', 'category_id', 'user_id'
        );
    }

    public function jobs() {
        return $this->belongsToMany(
            '\App\Models\Job', 'jobs_categories', 'category_id', 'job_id'
        );
    }

    public function quotes() {
        return $this->belongsToMany(
            '\App\Models\Quote', 'quotes_categories', 'category_id', 'quote_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    public function getSlugOrNameAttribute() {
        if ($this->slug != '') {
            return $this->slug;
        }
        return $this->name;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}


// class UsersCategory extends Category {
//     public static function boot() {
//         parent::boot();
//     }
// }
