<?php

namespace App\Http\Controllers\Telegram\Commands;

use App\Models\TelegramCanal;
use App\Models\Quote as QuoteModel;

/**
 * Abstracción del comando /quote del bot.
 *
 * Devuelve una cita aleatoria, opcionalmente permitiendo filtrar por
 * categorías.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Quote extends AbstractCommand
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
        if ( ! $this->isValidChannel()) {
            return;
        }

        /**
         * Get and send the quote
         */
        $arguments = $this->getArguments();
        $text      = QuoteModel::getAndMarkRandomQuoteText(
            $this->getChatId(),
            $arguments['category'] ?? null);
        $this->replyWithMessage(['text' => $text]);
    }
}