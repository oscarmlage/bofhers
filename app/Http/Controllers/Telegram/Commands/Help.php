<?php

namespace App\Http\Controllers\Telegram\Commands;

use App\Http\Controllers\Telegram\Commands\AbstractCommand as AbstractCommandAlias;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;

/**
 * Commands that displays the list of valid commands that the bot has available
 * and displays extra help from some commands.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Help extends AbstractCommand
{

    protected const VALID_COMMANDS = [
        'Addquote',
        'Anclado',
        'Canal',
        'ChatId',
        'Help',
        'Quote',
        'Repo',
        'Stats',
        'Version',
        'Web',
    ];

    protected $name = 'help';
    protected $aliases = ['menutifu', 'rtfm'];
    protected $description = 'Muestra los comandos a los que responde el bot.';
    protected $arguments_regexp = '/(?P<text>.*)/';
    public $long_help = 'Creo que necesitas un tipo de ayuda que no puedo darte 😥';

    /**
     * Displays the available non-hidden commands on the chat_id used by the
     * update that triggered this command.
     */
    protected function listCommands()
    {
        $this->replyWithMessage([
            'text' => 'A vé. Este es el listado de cosicas que hago:',
        ]);
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $commands = $this->getTelegram()->getCommands();
        $response = '';

        /**
         * @var $command AbstractCommand
         */
        foreach ($commands as $name => $command) {
            if ($command->isHidden()) {
                continue;
            }

            $response .= sprintf('- `/%s`: %s' . PHP_EOL, $name,
                $command->getDescription());
        }

        $this->replyWithMessage([
            'parse_mode' => 'markdown',
            'text'       => $response,
        ]);
    }

    /**
     * Taking the command name as an argument returns its FQDN, if the command
     * does exist.
     *
     * @param string $cmd
     *
     * @return string|null
     */
    protected function getCommandFQDN(string $cmd): ?string
    {
        $cmd = ucfirst(strtolower($cmd));

        if ( ! in_array($cmd, self::VALID_COMMANDS, true)) {
            Log::debug("${cmd} no es válido.");

            return null;
        }

        switch ($cmd) {
            case 'Chatid':
                $cmd = 'ChatId';
                break;

            case 'Addquote':
                $cmd = 'AddQuote';
                break;

            default:
                break;
        }

        $cmd = __NAMESPACE__ . '\\' . $cmd;
        Log::debug("namespace: ${cmd}");

        return class_exists($cmd) ? $cmd : null;
    }

    /**
     * Given the name of a command displays it's full help, if it has one.
     *
     * @param string $cmd_name
     */
    protected function showCommandHelp(string $cmd_name)
    {
        if ( ! $cmd = $this->getCommandFQDN($cmd_name)) {
            $this->replyWithErrorMessage(
                "No conozco el comando '${cmd_name}'. Mira a ver qué " .
                'hostias has escrito.'
            );

            return;
        }

        /**
         * @var $cmd AbstractCommandAlias
         */
        $cmd = App::make($cmd);

        if ($cmd->isHidden()) {
            $this->replyWithErrorMessage(
                "No conozco el comando '${cmd_name}'. Mira a ver qué " .
                'hostias has escrito.'
            );

            return;
        }

        if ( ! $cmd->long_help) {
            $this->replyWithErrorMessage(
                'No tengo más ayuda que darte para ese comando.'
            );

            return;
        }

        $this->replyWithMessage([
            'parse_mode' => 'markdown',
            'text'       => $cmd->long_help,
        ]);
    }

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $arguments = $this->getBofhersArguments();

        if (empty($arguments['text'])) {
            $this->listCommands();
        } else {
            $this->showCommandHelp($arguments['text'][0]);
        }
    }
}