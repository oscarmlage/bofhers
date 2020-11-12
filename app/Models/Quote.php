<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Builder;

class Quote extends Model
{

    public const QUOTE_STATUS_ALREADY_SAID = -1;

    public const QUOTE_STATUS_NOT_YET_SAID = 1;

    public const QUOTE_STATUS_NOT_VALIDATED = 0;

    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'quotes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }

    /**
     * Given an arbitrary chat_id and category_name, searches for a random quote
     * that matches the given criteria and has an active value of
     * QUOTE_STATUS_NOT_YET_SAID.
     *
     * In case there's not a quote with the criteria and that status, all of
     * the quotes that have a status of QUOTE_STATUS_ALREADY_SAID will be set
     * to QUOTE_STATUS_NOT_YET_SAID and an informative string message will be
     * returned as the quote.
     *
     * If there are errors (no quotes found or no such category slug) an
     * informative error will be returned as a quote.
     *
     * @param string      $chat_id       The chat id to which the quote must
     *                                   belong to.
     * @param string|null $category_name The category name to which the quote
     *                                   must belong to. If null, it will be
     *                                   ignored.
     *
     * @return string                    The text of the quote, or an
     *                                   informative status message if something
     *                                   went wrong.
     */
    public static function getAndMarkRandomQuoteText(
        string $chat_id,
        string $category_name = null
    ): string {
        $category = null;

        if ( ! is_null($category_name)) {
            if ( ! $category = Category::fromName('slug', $category_name)) {
                return '404: category not found';
            }
        }

        /**
         * Returns an Eloquent builder that selects all quotes for the passed
         * chat id and category id (if defined)
         *
         * @returns Builder
         */
        $_quoteBuilder = function () use ($chat_id, $category) {
            if ( ! is_null($category)) {
                $ids     = $category->quotes
                    ->where('chat_id', $chat_id)
                    ->map(function ($item, $key) {
                        return $item->id;
                    });
                $builder = static::whereIn('id', $ids);
            } else {
                $builder = static::where('chat_id', $chat_id);
            }

            return $builder;
        };

        // Sanity check for quotes on the given chat id + category
        if ($_quoteBuilder()
                ->where('active', '<>', Quote::QUOTE_STATUS_NOT_VALIDATED)
                ->count() == 0) {
            return '404: quote not found';
        }

        // There are indeed quotes. Let's try to find one that hasn't been seen
        $quote = $_quoteBuilder()
            ->where('active', static::QUOTE_STATUS_NOT_YET_SAID)
            ->orderByRaw("RAND()")
            ->limit(1)
            ->first();

        // There is one, just mark it as seen and return it
        if ($quote) {
            $quote->active = static::QUOTE_STATUS_ALREADY_SAID;
            $quote->save();

            return $quote->quote;
        }

        /**
         * If we are here it means that there are quotes for the given chat
         * and category but all of them have been seen already. At this point,
         * we label them all as unseen and return a message that informs of that
         * fact.
         */
        $_quoteBuilder()
            ->where('active', static::QUOTE_STATUS_ALREADY_SAID)
            ->update(['active' => static::QUOTE_STATUS_NOT_YET_SAID]);

        return 'Pasamos de fase, quotes reiniciados, nivel DOS';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function categories()
    {
        return $this->belongsToMany(
            'App\Models\Category', 'quotes_categories', 'quote_id',
            'category_id'
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
