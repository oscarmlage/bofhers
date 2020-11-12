<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\FileUpload\InputFile;

use \App\Models\Telegram as Tel;
use \App\Models\TelegramCanal as Canal;
use Throwable;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function handleRequest(Request $request)
    {
        /**
         * Everything is enclosed in a try-catch to prevent a requests
         * that triggers errors from getting stuck in the webhook and
         * causing the bot to become unresponsive.
         */
        try {
            $this->chat_id          = isset($request['message']['chat']['id']) ? $request['message']['chat']['id'] : 0;
            $this->username         = isset($request['message']['from']['username']) ? $request['message']['from']['username'] : 'no-username';
            $this->first_name       = isset($request['message']['from']['first_name']) ? $request['message']['from']['first_name'] : 'no-first-name';
            $this->last_name        = isset($request['message']['from']['last_name']) ? $request['message']['from']['last_name'] : 'no-last-name';
            $this->telegram_user_id = isset($request['message']['from']['id']) ? $request['message']['from']['id'] : 'no-telegram-id';
            $this->text             = isset($request['message']['text']) ? $request['message']['text'] : 'no-text';
            $this->message_id       = isset($request['message']['message_id']) ? $request['message']['message_id'] : '';

            /* $this->chat_id = $request['message']['chat']['id']; */
            /* $this->username = $request['message']['from']['username']; */
            /* $this->text = $request['message']['text']; */

            $data = [
                'chat_id'          => $this->chat_id,
                'nick'             => $this->username,
                'first_name'       => $this->first_name,
                'last_name'        => $this->last_name,
                'telegram_user_id' => $this->telegram_user_id,
                'text'             => $this->text,
                'request'          => $request
            ];
            $tel  = new Tel($data);
            $tel->save();

            try {
                $this->telegram->commandsHandler(true);
            } catch (Throwable $e) {
                Log::error(
                    $e->getMessage() . PHP_EOL . $e->getTraceAsString()
                );
                $this->sendMessage(
                    '❌ Alerta marrón. El despliegue ha salido octarino y ' .
                    'he petado.'
                );

                return;
            }


            // Allow commands only in oficial channels (not privates)
            $allowed_channels = Canal::where('active', 1)
                                     ->pluck('chat_id')
                                     ->toArray();
            if (in_array($this->chat_id, $allowed_channels)) {

                switch (true) {
                    // Deprecated commands (they all now use the same verb but starting with a "/")
                    case preg_match('/^!addquote .*/', $this->text) === 1:
                    case $this->text === '!stats':
                    case $this->text === '!chatid':
                    case $this->text === '!canal':
                    case $this->text === '!quote':
                    case $this->text === '!anclado':
                    case $this->text === '!repo':
                    case $this->text === '!help':
                    case $this->text === '!version':
                        $cmd = substr(explode(' ', $this->text)[0], 1);
                        $this->sendMessage("!${cmd} está deprecated. Pásate a /${cmd} o RTFM con /help");
                        break;

                    // Pesao de las AMI
                    case preg_match('/AMI.*/',
                            $this->text) === 1 && $this->telegram_user_id == '181121900':
                        $resource = fopen(public_path('/quotes/fifu.png'), 'r');
                        $filename = 'fifu.png';
                        $this->telegram->sendPhoto([
                            'chat_id'             => $this->chat_id,
                            'photo'               => InputFile::create($resource,
                                $filename),
                            'caption'             => 'Pesao, más que pesao, que nadie NADIE quiere tus 1star AMIs.',
                            'reply_to_message_id' => $this->message_id
                        ]);
                        break;
                    // COVID COVAD
                    case preg_match('/covid$/i', $this->text) === 1:
                        $this->sendMessage('COVAD! Cada día te quiero mad covid covid.... 🎼🎵🎼🎵🎶');
                        break;
                    default:
                }
            }
        }
        catch (Throwable $e) {
            Log::error(
                $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );
        }
    }

    public function showMenu($info = null)
    {
        $message = '';
        if ($info) {
            $message .= $info . chr(10);
        }
        $message .= '/menu' . chr(10);
        $this->sendMessage($message);
    }

    protected function sendMessage($message, $parse_html = false)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];
        if ($parse_html) $data['parse_mode'] = 'HTML';
        $this->telegram->sendMessage($data);
    }

}
