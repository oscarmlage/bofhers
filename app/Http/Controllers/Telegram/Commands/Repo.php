<?php


namespace App\Http\Controllers\Telegram\Commands;

/**
 * Command that shows a link to the bot's repository.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Repo extends AbstractCommand
{

    protected $name = 'repo';
    protected $description = 'Muestra el link del repositorio del bot.';

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $this->replyWithMessage(['text' => 'https://github.com/oscarmlage/bofhers']);
    }
}