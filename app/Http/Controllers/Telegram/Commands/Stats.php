<?php


namespace App\Http\Controllers\Telegram\Commands;

use App\Models\Quote as Quote;

/**
 * Commands that shows stats about the quotes on the tdatabase.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Stats extends AbstractCommand
{

    protected $name = 'stats';
    protected $description = 'Muestra estadísticas de las citas del canal.';

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        if ( ! $id = $this->getChatId()) {
            return;
        }

        $all = count(Quote::where('chat_id', $id)
                          ->get());

        $said = count(Quote::where('chat_id', $id)
                           ->where('active',
                               Quote::QUOTE_STATUS_ALREADY_SAID)
                           ->get());

        $not_said = count(Quote::where('chat_id', $id)
                               ->where('active',
                                   Quote::QUOTE_STATUS_NOT_YET_SAID)
                               ->get());

        $pending = count(Quote::where('chat_id', $id)
                              ->where('active',
                                  Quote::QUOTE_STATUS_NOT_VALIDATED)
                              ->get());

        $text = '🔷️ All Quotes: <b>' . $all .
                '</b> 🤪️ Said Quotes: <b>' . $said .
                '</b> 🤫️ Not said quotes: <b>' . $not_said .
                '</b> 🔴️ Not validated yet: <b>' . $pending . '</b>';
        $this->answerWithMessage($text, ['parse_mode' => 'html']);
    }
}