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
     * Given an arbitrary chat id, searchs for a random quote that matchs the
     * given ID and is marked as "not yet said".
     *
     * If there is such a quote, it will be marked as "already said" and
     * returned.
     *
     * If there's no such quote, all of the "already seen quotes" will be marked
     * as unseen and an informative text will be returned.
     *
     * In case there's no valid quotes (seen or unseen) an error message will
     * be returned.
     *
     * @param string      $chat_id       The chat id to which the quote must
     *                                   belong to
     * @param string|null $category_slug The category slug to which the quote
     *                                   must belong to. If null, it will be
     *                                   ignored.
     *
     * @return string
     */
    public static function getAndMarkRandomQuoteText(
        string $chat_id,
        string $category_slug = null
    ): string {
        /**
         * Returns an Eloquent builder that selects all quotes for the passed
         * chat id, category id (if defined) that are validated.
         *
         * @returns Builder
         */
        $_quoteBuilder = function () use ($chat_id, $category_slug) {
            if ( ! is_null($category_slug)) {
                $category = Category::where('slug', $category_slug)->first();
                $builder  = $category->quotes->where('chat_id', $chat_id);
            } else {
                $builder = static::where('chat_id', $chat_id);
            }

            $builder = $builder->where(
                'active', '<>', Quote::QUOTE_STATUS_NOT_VALIDATED);

            return $builder;
        };

        // Sanity check for quotes on the given chat id + category
        if ($_quoteBuilder()->get()->count() == 0) {
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
            ->where('active', '=',
                static::QUOTE_STATUS_ALREADY_SAID)
            ->update(['active' => static::QUOTE_STATUS_NOT_YET_SAID]);

        return 'Pasamos de fase, quotes reiniciados, nivel DOS';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function categories() {
        return $this->belongsToMany(
            'App\Models\Category', 'quotes_categories', 'quote_id', 'category_id'
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
