<?php


namespace App\Http\Controllers\Telegram\Commands;

/**
 * Comando para mostrar la web asociada al canal en el que se invoque.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Web extends AbstractCommand
{

    protected $name = 'web';
    protected $description = 'Muestra la web asociada al canal.';

    public function handlerBofhers(array $arguments = null)
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        if ( ! $canal = $this->getChannel()) {
            return;
        }

        $this->answerWithMessage(
            $canal->web ?? 'No hay web asociada, HOSTIA YA.'
        );
    }
}