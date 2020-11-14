<?php


namespace App\Http\Controllers\Telegram\Commands;


/**
 * This doesn't need much clarification as to what it does.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Anclado extends AbstractCommand
{

    protected $name = 'anclado';
    protected $description = 'Muestra información muy interesante.';

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $this->answerWithMessage('¡El que tengo aquí colgado! 🍆');
    }
}