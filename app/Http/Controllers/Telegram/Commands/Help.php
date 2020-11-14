<?php

namespace App\Http\Controllers\Telegram\Commands;

use App\Http\Controllers\Telegram\Commands\AbstractCommand as AbstractCommandAlias;
use App\Http\Controllers\Telegram\Commands\Exceptions\UnknownCommandException;
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
    protected $arguments_regexp = '/(?P<text>.*)/s';
    public $long_help = 'Creo que necesitas un tipo de ayuda que no puedo darte 😥';

    /**
     * Displays the available non-hidden commands on the chat_id used by the
     * update that triggered this command.
     */
    protected function listCommands()
    {
        $this->answerWithMessage(
            'A vé. Este es el listado de cosicas que hago:'
        );
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

        $response .= PHP_EOL . 'Algunos comandos disponen de ayuda extendida ' .
                     'con `/help <comando>`.';

        $this->answerWithMessage($response, ['parse_mode' => 'markdown']);
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

        $this->answerWithMessage($cmd->long_help, ['parse_mode' => 'markdown']);
    }

    public function handlerBofhers(array $arguments = null)
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        if (empty($arguments['text'])) {
            $this->listCommands();
        } else {
            $this->showCommandHelp($arguments['text'][0]);
        }
    }
}