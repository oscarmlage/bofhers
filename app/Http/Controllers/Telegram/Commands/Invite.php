<?php

namespace App\Http\Controllers\Telegram\Commands;

use \App\Models\TelegramCanal as TelegramCanal;

/**
 * Command that shows a link to the bot's repository.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Invite extends AbstractCommand
{

    protected $name = 'invite';
    protected $description = 'Muestra el enlace de invitación del canal';

    public function handlerBofhers(array $arguments = null)
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $chatid = $this->getChatId();
        $grupo = TelegramCanal::where('chat_id', $chatid)->firstOrFail();
        if($grupo && $grupo->invitelink) {
            $this->answerWithMessage($grupo->invitelink);
        }
    }
}
