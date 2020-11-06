<?php


namespace App\Http\Controllers\Telegram\Commands;

use \App\Models\Quote;

/**
 * Command that adds a random quote to the list of the current channel.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class AddQuote extends AbstractCommand
{

    protected $name = 'addquote';
    protected $description = 'Añade una cita sin validar a la lista.';

    /**
     * Validates an incoming quote returning a string describing the validation
     * error if it's not valid and null if it is indeed valid.
     *
     * @param string $text The quote to validate
     *
     * @return string|null
     */
    protected function getProblemsWithQuote(string $text): ?string
    {
        if (empty($text)) {
            return 'Pezqueñines no, gracias... ¡hay que dejarlos crecer! 🤷';
        }

        return null;
    }

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $update = $this->getUpdate();
        $text   = trim($update->message->text);

        if ($error = $this->getProblemsWithQuote($text)) {
            $this->replyWithMessage(['text' => "❌ ${error}"]);

            return;
        }

        $data = ([
            'chat_id'          => $update->getChat()->id,
            'nick'             => $update->message->from->username,
            'first_name'       => $update->message->from->firstName,
            'last_name'        => $update->message->from->lastName,
            'telegram_user_id' => $update->message->from->id,
            'quote'            => $text,
            'active'           => Quote::QUOTE_STATUS_NOT_VALIDATED,
        ]);

        $quote = new Quote($data);


        if ( ! $quote->save()) {
            $this->replyWithMessage([
                'text' => 'Pos ahora peto y no me da la gana de hacerte caso, ' .
                          $update->message->from->username . '.',
            ]);

            return;
        }

        $this->replyWithMessage([
            'text' => '✅ Quote agregado... ¡y lo llevo aquí colgado!',
        ]);
    }
}