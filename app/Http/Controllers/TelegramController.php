<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api as Telegram;

use \App\Models\Telegram as Tel;
use \App\Models\TelegramCanal as Canal;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        //$this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $this->telegram = new Telegram(
            env('TELEGRAM_BOT_TOKEN')
        );
    }

    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function setWebHook()
    {
        $url = env('TELEGRAM_WEBHOOK_URL') . env('TELEGRAM_BOT_TOKEN') . '/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response == true ? redirect()->back() : dd($response);
    }


    public function handleRequest(Request $request)
    {
        file_put_contents('telegram.log', $request);
        $this->chat_id = isset($request['message']['chat']['id']) ? $request['message']['chat']['id'] : 0;
        $this->username = isset($request['message']['from']['username']) ? $request['message']['from']['username'] : 'no-username';
        $this->first_name = isset($request['message']['from']['first_name']) ? $request['message']['from']['first_name'] : 'no-first-name';
        $this->last_name = isset($request['message']['from']['last_name']) ? $request['message']['from']['last_name'] : 'no-last-name';
        $this->telegram_user_id = isset($request['message']['from']['id']) ? $request['message']['from']['id'] : 'no-telegram-id';
        $this->text = isset($request['message']['text']) ? $request['message']['text']: 'no-text';

        /* $this->chat_id = $request['message']['chat']['id']; */
        /* $this->username = $request['message']['from']['username']; */
        /* $this->text = $request['message']['text']; */

        $data = [
            'chat_id'=>$this->chat_id,
            'nick'=>$this->username,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'telegram_user_id'=>$this->telegram_user_id,
            'text'=>$this->text,
            'request'=>$request
        ];
        $tel = new Tel($data);
        $tel->save();
        switch ($this->text) {
            case 'ahoy tifu':
                $this->sendMessage('Ahoy Matey! So, Ye want t\' talk like a
                    pirate aye...');
                break;
            case 'hi tifu':
                $this->sendMessage('Ahoy Matey! So, Ye want t\' talk like a pirate aye...');
                break;
            case 'joke tifu':
                $this->sendMessage('What is a robot\'s favorite type of music?... Heavy metal!');
                break;
            case '/menutifu':
                $this->sendMessage('Aquí debería ir el menú con los comandos disponibles');
                //$this->showMenu();
                break;
            case '!canal':
                $canal = Canal::where('chat_id', $this->chat_id)->firstOrFail();
                $this->sendMessage($canal);
                break;
           default:
                /* $this->showMenu(); */
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
