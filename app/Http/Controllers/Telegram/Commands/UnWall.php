<?php

namespace App\Http\Controllers\Telegram\Commands;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Command that adds a random quote to the list of the current channel.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class UnWall extends AbstractCommand
{

    /**
     * Max length used by a category from this command
     */
    protected $name = 'unwall';
    protected $description = 'Ofrece proxies para una URL de un medio online ' .
                             'para intentar eliminar los clásicos paywalls.';


    public $long_help = <<<HELP
    - `/unwall <URL>` - Una URL válida.

    El comando devolverá propuestas de enlaces para saltar el paywall
HELP;

    /**
     * This is used to parse the arguments on the command, which are:
     *
     * - "url": an arbitrary string with the URL we want to "fix"
     *
     * @var string
     */
    protected $arguments_regexp = '/(?P<url>.*?)?$/s';

    /**
     * Validates an incoming quote returning a string describing the validation
     * error if it's not valid and null if it is indeed valid.
     *
     * @param string|null $url The quote url to validate
     *
     * @return string|null
     */
    protected function getProblemsWithURL(?string $url): ?string
    {
        if (is_null($text)) {
            return 'A ver, que no es complicado: /unwall **AQUI_LA_URL_LECHES**';
        }

        if (empty($text)) {
            return 'Pero vamos a ver... la madre que te... : /unwall **AQUI_LA_URL_LECHES**';
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
        	return 'Erm... a tí eso te parece una URL? ';
        }

        return null;
    }

    /**
     * Validates a URL text to make sure that it is valid.
     *
     * The function will return a human-readable error in case any validation
     * problem occurs and will return null if there are no problems.
     *
     * @param string|null $url         The URL text to unwall
     *
     * @return string|null
     */
    protected function getValidationError(
        ?string $url
    ): ?string {
        if ($err = $this->getProblemsWithURL($quote)) {
            return $err;
        }
    }

    public function handlerBofhers(array $arguments = null)
    {

        $url          = $arguments['url'][0] ?? null;

        // Simple and trivial validation
        if ($error = $this->getValidationError($url)) {
            $this->replyWithErrorMessage($error);

            return;
        }

        $this->answerWithMessage(
            'Sus enlaces, gracias!
            📰 https://12ft.io/proxy?q='.$url.'
			📄 https://txtify.it/'.$url
        );
    }
}
