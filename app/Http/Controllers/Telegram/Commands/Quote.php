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
    protected $arguments_regexp = '/(?P<tag>.*)?/';

    /**
     * @var string
     */
    protected $description = 'Devuelve una cita aleatoria de una categoría.';

    public $long_help = <<<HELP
    - `/quote` - Muestra una cita aleatoria.
    - `/quote <categoria>` - Muestra una cita aleatoria de una categoría dada.
        
    Este práctico comando mostrará en el canal una de esas perlas nacaradas de sabiduría efímera que he ido almacenando con los años. Si se especifica una categoría la cita pertenecerá a la misma.
    
    A la hora de mostrar citas procuraré mostrar aquellas que no hayan sido vistas recientemente. Solo cuando haya enviado todas las que correspondan a la categoría dejaré todas otra vez como 'no leidas'. ¿Capito? 
    HELP;

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
        $arguments = $this->getBofhersArguments();
        $text      = QuoteModel::getAndMarkRandomQuoteText(
            $this->getChatId(),
            $arguments['tag'][0] ?? null);
        $this->replyWithMessage(['text' => $text]);
    }
}