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
     * The regex that will be used by the getBofhersArguments() to parse
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
        $this->answerWithMessage(['text' => "❌ ${error}"]);
    }

    /**
     * The Telegram SDK for PHP that this project uses has a getArguments()
     * implementation that, unfortunatly, lacks a few features to make it
     * flexible.
     *
     * Because of that, this method exists. It parses the text line from the
     * Update that triggered the commands and extracts the variables inside it
     * according to the $arguments_regexp variable.
     *
     * The result will be a dictionary with the results of using the
     * preg_match_all function with those parameters. It is recommended to use
     * capture groups to make it easier to work.
     *
     * @return array Parsed arguments from the Update
     * @see \preg_match_all()
     * @see $this->arguments_regexp
     */
    protected function getBofhersArguments(): array
    {
        $message = trim($this->getUpdate()->message->text ?? '');
        $matches = [];

        // Remove the preceding command name from the text and save the raw args
        $names   = implode(array_merge($this->aliases, [$this->name]), '|');
        $pattern = '\/(?:' . $names . ')\s*(?P<raw_args>.*)?';

        preg_match_all("/^${pattern}$/s", $message, $matches);

        if ( ! isset($matches["raw_args"][0])) {
            throw new UnknownCommandException(
                "Comando desconocido para: '${message}'."
            );
        }

        $message = trim($matches["raw_args"][0]);

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
     * @see AbstractCommand::getBofhersArguments()
     * @return mixed
     */
    abstract public function handlerBofhers(array $bofhersArguments = null);

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

        $this->handlerBofhers($this->getBofhersArguments());
    }
}