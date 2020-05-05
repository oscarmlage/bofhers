<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api as Telegram;

use \App\Models\Category;
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
     * @return mixed
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
            $this->chat_id = $canal->chat_id;
            $quote = Quote::where('chat_id', $this->chat_id)->where('active', 1)->orderByRaw("RAND()")->limit(1)->first();
            if($tag) {
                $tag = Category::where('slug', $tag)->first();
                $quote = $tag->quotes()->where('chat_id', $this->chat_id)->where('active', 1)->orderByRaw("RAND()")->limit(1)->first();
            }

            $data = [
                'chat_id' => $this->chat_id,
                'parse_mode' => 'HTML',
                'text' => $quote->quote,
            ];
            $this->telegram->sendMessage($data);
        }

    }
}

