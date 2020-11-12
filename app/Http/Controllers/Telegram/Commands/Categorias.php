<?php


namespace App\Http\Controllers\Telegram\Commands;

use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use function count;

/**
 * Commands that display the categories with active quotes and the total count
 * of their associated quotes.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Categorias extends AbstractCommand
{

    /**
     * Name used to group uncategorized tags under
     */
    public const UNCATEGORIZED_NAME = 'sin categoría';

    protected $name = 'categorias';
    protected $description = 'Muestra las categorías con citas validadas.';


    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $sql = <<<SQL
            SELECT ifnull(name, ?) as name, count(*) as quotes
            FROM categories
            RIGHT OUTER JOIN quotes_categories qc on qc.category_id = categories.id
            RIGHT OUTER JOIN quotes q on qc.quote_id = q.id
            WHERE q.active <> ?
            GROUP BY name
            HAVING count(*) > 0
            ORDER BY count(*) DESC
        SQL;

        $categories = DB::select(
            $sql, [
                self::UNCATEGORIZED_NAME,
                Quote::QUOTE_STATUS_NOT_VALIDATED,
            ]
        );

        if ( ! count($categories)) {
            $this->replyWithErrorMessage(
                "No parece que haya citas validadas con categorías."
            );

            return;
        }

        $categories = array_map(
            function ($data) {
                return $data->name . ' (' . $data->quotes . ')';
            },
            $categories
        );

        $categories = implode($categories, ', ');
        $this->replyWithMessage(['text' => $categories]);
    }
}