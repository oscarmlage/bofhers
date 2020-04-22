<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api as Telegram;

use \App\Models\Telegram as Tel;

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
        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];
        $data = ['nick'=>$this->username, 'chat_id'=>$this->chat_id, 'text'=>$this->text];
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
