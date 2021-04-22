<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api as Telegram;
use \App\Models\Quote;
use \App\Models\TelegramCanal;


class say extends Command
{
    protected $telegram;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:say {canal?} "{text?}"';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Say / send a text to a channel';

    /**
     * Create a new command instance.
     *
     * @return void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function __construct()
    {
        $this->telegram = new Telegram(
            env('TELEGRAM_BOT_TOKEN')
        );

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $canal = $this->argument('canal');
        $text = $this->argument('text');

        if($canal) {
            $group = TelegramCanal::where('name', "#".$canal)->where('active', 1)->first();
        }

        // Send the thing only if text exists and is not empty
        if($text && $group) {
            $data = [
                'chat_id' => $group->chat_id,
                'parse_mode' => 'HTML',
                'text' => $text,
            ];

            $this->telegram->sendMessage($data);
        }

    }
}

