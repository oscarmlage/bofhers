<?php


namespace App\Http\Controllers\Telegram\Commands;

/**
 * Comnmand that displays the chat ID for this command
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class ChatId extends AbstractCommand
{

    protected $hidden = true;
    protected $name = 'chatid';
    protected $description = 'Muestra el ID telegram para el canal';

    protected $pattern = '(?P<numero>[0-9]+) (?P<texto>[a-z]+)';

    /**
     * @inheritDoc
     */
    public function handlerBofhers(array $arguments = null)
    {
        $text = $this->getChatId() . '';

        $this->answerWithMessage($text, (['parse_mode' => 'html']));
    }
}