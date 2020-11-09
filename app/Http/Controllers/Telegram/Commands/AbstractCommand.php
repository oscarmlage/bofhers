<?php


namespace App\Http\Controllers\Telegram\Commands;

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
     * @var bool
     */
    protected $hidden = false;
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
     * @return bool
     */
    protected function isHidden(): bool
    {
        return $this->hidden;
    }
}