<?php

namespace App\Http\Controllers\Telegram\Commands;

use App\Models\TelegramCanal;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use App\Models\Quote as QuoteModel;

/**
 * Abstracción del comando /quote del bot.
 *
 * Devuelve una cita aleatoria, opcionalmente permitiendo filtrar por
 * categorías.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Quote extends Command
{

    /**
     * @var \App\Models\Quote
     */
    protected $model;

    /**
     * @var string
     */
    protected $name = 'quote';

    /**
     * @var string
     */
    protected $pattern = '{category}';

    /**
     * @var string
     */
    protected $description = 'Devuelve una cita aleatoria de una categoría.';

    public function __construct(QuoteModel $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        /**
         * Make sure that the incoming update has a valid id and it is for
         * an active channel.
         */
        if ( ! $this->update->getChat()->has('id')) {
            return;
        }

        $chatId = $this->update->getChat()->id;
        $canal  = TelegramCanal::where([
            ['chat_id', '=', $chatId],
            ['active', '=', 1],
        ])->first();

        if ( ! $canal) {
            return;
        }

        /**
         * Get and send the quote
         */
        $arguments = $this->getArguments();
        $text      = QuoteModel::getAndMarkRandomQuoteText($chatId,
            $arguments['category'] ?? null);
        $this->replyWithMessage(['text' => $text . " " . $chatId]);
    }
}