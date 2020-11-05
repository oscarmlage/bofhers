<?php


namespace App\Http\Controllers\Telegram\Commands;

use Telegram\Bot\Actions;

/**
 * Commands that displays the list of valid commands that the bot has available.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Help extends AbstractCommand
{

    protected $name = 'help';
    protected $aliases = ['menutifu'];
    protected $description = 'Muestra los comandos a los que responde el bot.';

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $this->replyWithMessage(['text' => 'Amof a ver. Efto ef lo que puedo hacer:']);
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $commands = $this->getTelegram()->getCommands();
        $response = '';

        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name,
                $command->getDescription());
        }

        $this->replyWithMessage(['text' => $response]);
    }
}