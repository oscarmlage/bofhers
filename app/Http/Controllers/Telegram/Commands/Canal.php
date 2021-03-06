<?php


namespace App\Http\Controllers\Telegram\Commands;

use \App\Models\TelegramCanal as TelegramCanal;

/**
 * Command that shows the description of the channel that triggered the update
 * according to the TelegramCanal model.
 *
 * @see     TelegramCanal
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Canal extends AbstractCommand
{

    protected $name = 'canal';
    protected $description = 'Muestra información del canal en el que me ' .
                             'encuentro.';

    /**
     * @inheritDoc
     */
    public function handlerBofhers(array $arguments = null)
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        if ( ! $id = $this->getChatId()) {
            return;
        }

        $canal = $this->getChannel();
        $this->answerWithMessage(
            $canal->description ??
            'Léete el puto menú del canal que no estoy para ' .
            'atender tus tonterías.'
        );
    }
}