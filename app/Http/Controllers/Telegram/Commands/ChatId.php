<?php


namespace App\Http\Controllers\Telegram\Commands;

use Illuminate\Support\Facades\Log;

/**
 * Comnmand that displays the chat ID for this command
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class ChatId extends AbstractCommand
{

    protected $name = 'chatid';
    protected $description = 'Muestra el ID telegram para el canal';

    protected $pattern = '(?P<numero>[0-9]+) (?P<texto>[a-z]+)';

    /**
     * @inheritDoc
     */
    public function handle()
    {
        //$text = \json_encode($this->getUpdate(), JSON_PRETTY_PRINT);
        $text = $this->getChatId() . '';

        $this->replyWithMessage(([
            'parse_mode' => 'html',
            'text'       => $text,
        ]));
    }
}