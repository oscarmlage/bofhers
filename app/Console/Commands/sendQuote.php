<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api as Telegram;

use \App\Models\Quote;
use \App\Models\TelegramCanal;


class sendQuote extends Command
{
    protected $telegram;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:sendquote {canal?} {tag?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send random quote to a channel (tag is optional)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $this->telegram = new Telegram(
            env('TELEGRAM_BOT_TOKEN')
        );
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $canal = $this->argument('canal');
        $tag = $this->argument('tag');
        $canales = TelegramCanal::where('active', 1)->get();
        if($canal) {
            $canales = TelegramCanal::where('name', "#".$canal)->where('active', 1)->get();
        }

        foreach($canales as $canal){
            $quote = Quote::getAndMarkRandomQuoteText($canal->chat_id, $tag ?? null);

            $data = [
                'chat_id' => $canal->chat_id,
                'parse_mode' => 'HTML',
                'text' => $quote,
            ];

            $this->telegram->sendMessage($data);
        }

    }
}

