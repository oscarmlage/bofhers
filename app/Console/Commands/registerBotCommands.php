<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api as Telegram;


/**
 * Takes every Telegram command registered in Laravel and sends an API request
 * to make them available to their respective bots.
 *
 * See ./config/telegram.php for more information.
 *
 * @package App\Console\Commands
 */
final class registerBotCommands extends Command
{

    /**
     * @var \Telegram\Bot\Api
     */
    protected $telegram;

    /**
     * @var string
     */
    protected $signature = 'telegram:registerBotCommands';

    /**
     * @var string
     */
    protected $description = 'Registers the bots commands on Telegram\'s API';

    /**
     * @param \Telegram\Bot\Api $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
        parent::__construct();
    }

    /**
     * Handles the call and spits to stdout the result from Telegram.
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $commands = $this->telegram->getCommands();
        $ret      = [];

        foreach ($commands as $name => $command) {
            $ret[] = [
                'command'     => $name,
                'description' => $command->getDescription(),
            ];
        }

        $this->telegram->setMyCommands(['commands' => $ret]);
    }
}
