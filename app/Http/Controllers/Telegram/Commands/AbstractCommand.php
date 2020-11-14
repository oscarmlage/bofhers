<?php


namespace App\Http\Controllers\Telegram\Commands;

use App\Http\Controllers\Telegram\Commands\Exceptions\UnknownCommandException;
use App\Models\TelegramCanal;
use Telegram\Bot\Commands\Command;

/**
 * A base Telegram command specifically designed to reuse some code used amongst
 * the bofh-commands.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
abstract class AbstractCommand extends Command
{

    /**
     * Hidden commands won't be shown on the /help command
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * The regex that will be used by the parseBofhersArguments() to parse
     * variables from the command line when receiving a command.
     *
     * @var null|string
     */
    protected $arguments_regexp = null;

    /**
     * The text that will be shown whenever someone requests help for this
     * one specific command. If it is null help won't be shown.
     *
     * @var string|null
     */
    public $long_help = null;

    /**
     * Returns the TelegramCanal object associated with the chat ID given
     * in the Telegram's update that triggered this command and is properly
     * enabled in the application's database.
     *
     * If none can be found null will be returned.
     *
     * @return \App\Models\TelegramCanal|null
     */
    protected function getChannel(): ?TelegramCanal
    {
        if ( ! $id = $this->getChatId()) {
            return null;
        }

        return TelegramCanal::where([
            ['chat_id', '=', $id],
            ['active', '=', 1],
        ])->first();
    }

    /**
     * Queries the underlying update that triggered the comand's execution and
     * tries to find the chat ID that originated it.
     *
     * If found, it will be returned.
     *
     * @return int|null
     */
    protected function getChatId(): ?int
    {
        if ( ! $this->update->getChat()->has('id')) {
            return null;
        }

        return $this->update->getChat()->id;
    }

    /**
     * Queries the underlying update that triggered the command and check if
     * it was originated from a valid channel. That is: a channel that is
     * properly registered and active in the application.
     *
     * @return bool
     */
    protected function isValidChannel(): bool
    {
        if ( ! $chatId = $this->getChatId()) {
            return false;
        }

        $canal = TelegramCanal::where([
            ['chat_id', '=', $chatId],
            ['active', '=', 1],
        ])->first();

        return ! empty($canal);
    }

    /**
     * Returns true if the command shouldn't be shown on the /help command
     *
     * @return bool
     */
    protected function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Sends a reply to the message that triggered this command with a given
     * text and an optional set of parameters.
     *
     * @param string $message The text message to be sent.
     * @param array  $config  Optional parameters to the sendMessage API call
     *                        that will be used.
     *
     * @link https://core.telegram.org/bots/api#sendmessage
     */
    protected function answerWithMessage(
        string $message,
        array $config = []
    ) {
        $data = [
            'text'                => $message,
            'reply_to_message_id' => $this->update->getMessage()->messageId,
        ];
        $data = array_merge($data, $config);
        $this->replyWithMessage($data);
    }

    /**
     * Given an arbitrary string -suppossed to be an error message- sends the
     * given string to the channel that triggered the current command's
     * execution.
     *
     * @param string $error The error string to send to the channel
     */
    protected function replyWithErrorMessage(string $error)
    {
        $this->answerWithMessage("❌ ${error}");
    }

    /**
     * Parses the arguments of the command (that is: the text that is not
     * the command name) with the pattern given by $this->arguments_regexp
     * and returns any matches.
     *
     * @param string $args
     *
     * @return array Parsed array from the text argument
     * @see       $this->arguments_regexp
     * @see       \preg_match_all()
     */
    protected function parseBofhersArguments(string $args): array
    {
        $message = trim($args);

        if (empty($message) || empty($this->arguments_regexp)) {
            return [];
        }

        if ( ! $this->arguments_regexp) {
            return ['text' => $message];
        }

        preg_match_all($this->arguments_regexp, $message, $matches);

        return $matches;
    }

    /**
     * Implement this to handle an incoming command.
     *
     * @param array|null $bofhersArguments The bofhersArguments array
     *
     * @return mixed
     * @see AbstractCommand::parseBofhersArguments()
     */
    abstract public function handlerBofhers(array $bofhersArguments = null);

    /**
     * Parses the raw text line in the update that triggered the command call
     * and returns an array with the following contents:
     *
     * [
     *  'cmd'    => (?string):  name of the command or null if it does not match
     *                          our name or one of our aliases
     *  'botname => (?string):  bot name that the command is explicitely using
     *                          (/command@BotName) or null if the command is not
     *                          actively invoking a bot (/command)
     *  'args'   => (?string):  text with any extra arguments passed to /command
     *                          or null if there's none.
     * ]
     *
     * @return array|null[]
     */
    protected function parseBofhersCommand(): array
    {
        $message = trim($this->getUpdate()->message->text ?? '');
        $matches = [];

        // Remove the preceding command name from the text and save the raw args
        $names   = implode(array_merge($this->aliases, [$this->name]), '|');
        $pattern = "\/(?P<cmd>${names})(?P<botname>@.*bot)?\s*(?P<args>.*)?";

        preg_match_all("/^${pattern}$/is", $message, $matches);

        return [
            'cmd'     => $matches['cmd'][0] ?? null,
            'botname' => $matches['botname'][0] ?? null,
            'args'    => $matches['args'][0] ?? null,
        ];
    }

    /**
     * This method is used by the Telegram's library to invoke commands.
     *
     * As there are certain things that we do not wish to enforce on Bofher's
     * commands that the library forces us to do (for example: a single update
     * with several commands triggers all of them) we tweak it to suit our
     * own preferences.
     */
    public function handle()
    {
        /**
         * If the offset is not 0, it means that we are dealing with a command
         * in the middle of the text, which we do not wish to answer to.
         *
         * For example, the text: "hello this is a /command" would trigger an
         * command with an offset of 16.
         */
        if ($this->entity['offset'] !== 0) {
            return;
        }

        $command_parts = $this->parseBofhersCommand();

        // Command invoked does not match neither our name nor one of our aliases
        if (is_null($command_parts['cmd'])) {
            return;
        }

        // We are explicitely calling a botname that is not us.
        if ( ! empty($command_parts['botname'])) {
            $my_botname = "@" . $this->telegram->getMe()->username;

            if ($my_botname !== $command_parts['botname']) {
                return;
            }
        }
        $this->handlerBofhers($this->parseBofhersArguments($command_parts['args']));
    }
}