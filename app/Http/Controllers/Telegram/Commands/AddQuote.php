<?php

namespace App\Http\Controllers\Telegram\Commands;

use App\Models\Category;
use \App\Models\Quote;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Command that adds a random quote to the list of the current channel.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class AddQuote extends AbstractCommand
{

    /**
     * Max length used by a category from this command
     */
    public const MAX_CATEGORY_LENGTH = 40;

    protected $name = 'addquote';
    protected $description = 'Añade una cita sin validar a la lista con una ' .
                             'categoría opcional.';


    public $long_help = <<<HELP
    - `/addquote <texto>` - Añadir una cita. 
    - `/addquote <texto> %% <categoria>` - Añadir una cita y asociarle una categoría.
        
    El comando añade una cita al montón para que pueda mostrarse al usar el comando `/quote`.
        
    La categoría es opcional y puede utilizarse posteriormente con `/quote <categoria>`.

    Las citas añadidas han de ser validadas previo a ser mostradas.
    HELP;

    /**
     * This is used to parse the arguments on the command, which are:
     *
     * - "text": an arbitrary string with the quote's message
     * - "category": the category that will be assigned to this message.
     *
     * <category> is optional and might not exist.
     *
     * <text> and <category> must be separated by two consecutive "%%"
     * characters with 1 ore more blank space characters before and after.
     *
     * The following strings are valid:
     *
     * - this is a test message without a category
     * - this is a test message with a category named "test" %% test
     * - this is a
     *   multiline test message
     *   with a category named 'multiline' %% multiline
     * - finally, the same again, but with random whitespace      %%   test
     *
     * @var string
     */
    protected $arguments_regexp = '/(?P<text>.*?)(\s+%%\s+(?P<category>.*))?$/s';

    /**
     * Validates an incoming quote returning a string describing the validation
     * error if it's not valid and null if it is indeed valid.
     *
     * @param string|null $text The quote text to validate
     *
     * @return string|null
     */
    protected function getProblemsWithQuote(?string $text): ?string
    {
        if (is_null($text)) {
            return 'A ver, que no es complicado añadir una cita. Escribe ' .
                   'el texto "e ya". Hazlo despacio para no hacerte daño.';
        }

        if (empty($text)) {
            return 'Pezqueñines no, gracias... ¡hay que dejarlos crecer! 🤷';
        }

        return null;
    }

    /**
     * Given a category name that could be added to a tag, verifies that it has
     * a name with a little bit of sanity to prevent users from adding arbitrary
     * strings that make no sense.
     *
     * @param string|null $category The category name to validate
     *
     * @return string|null
     */
    protected function getProblemsWithCategory(?string $category): ?string
    {
        if (empty($category)) {
            return 'Hay cosas que trago y cosas que no. Categorías nulas ' .
                   'son una de las que no.';
        }

        if (1 !== preg_match('/^[\pL0-9]+$/ui', $category)) {
            return 'No voy a guardar categorías llenas de basura: solo ' .
                   'carácteres alfanuméricos y espacios.';
        }

        if (strlen($category) > self::MAX_CATEGORY_LENGTH) {
            return 'Esa categoría no me entra aunque hagas fuerza.';
        }

        $pattern = '^' . Categorias::UNCATEGORIZED_NAME . '$';

        if (preg_match("/${pattern}/i", $category) === 1) {
            return '¡Bravoooo!, ¡muy bien!, ¡qué ingenio!, ' .
                   'aplauso para tamaña genialidad 👏🏾👏🏾👏🏾 /s';
        }

        return null;
    }

    /**
     * Validates both a quote text and it's category name to make sure that
     * they are both valid.
     *
     * The function will return a human-readable error in case any validation
     * problem occurs and will return null if there are no problems.
     *
     * @param string|null $quote         The quote text to validate
     * @param string|null $category_name The category name to validate
     *
     * @return string|null
     */
    protected function getValidationError(
        ?string $quote,
        ?string $category_name
    ): ?string {
        if ($err = $this->getProblemsWithQuote($quote)) {
            return $err;
        }

        // Null categories will be ignored as that means an uncategorized quote
        if (empty($category_name)) {
            return null;
        }

        return $this->getProblemsWithCategory($category_name);
    }

    /**
     * Given an arbitrary string, creates a new quote with the reset of its
     * information taken from the Telegram's update that triggered the current
     * command's execution.
     *
     * Returns the create Model or null, if there were errors.
     *
     * @param string $text The new quote text
     *
     * @return \App\Models\Quote
     * @see Quote
     */
    protected function createNewQuote(string $text): ?Quote
    {
        $update = $this->getUpdate();
        $quote  = new Quote([
            'chat_id'          => $update->getChat()->id,
            'nick'             => $update->message->from->username,
            'first_name'       => $update->message->from->firstName,
            'last_name'        => $update->message->from->lastName,
            'telegram_user_id' => $update->message->from->id,
            'quote'            => $text,
            'active'           => Quote::QUOTE_STATUS_NOT_VALIDATED,
        ]);

        return $quote->save() ? $quote : null;
    }

    /**
     * Given a category name (which is assumed to be valid), the function will
     * try and search for an existing category with such a name.
     *
     * If there are no results, the function will create a new category with
     * that name and return it.
     *
     * In case there are any errors in the creation process the function will
     * return null,
     *
     * @param string $category_name The category name
     *
     * @return \App\Models\Category|null
     */
    protected function getOrCreateCategory(string $category_name): ?Category
    {
        if ($category = Category::fromName($category_name)) {
            return $category;
        }

        $category = new Category(['name' => $category_name]);

        return $category->save() ? $category : null;
    }

    public function handle()
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $arguments     = $this->getBofhersArguments();
        $text          = $arguments['text'][0] ?? null;
        $category_name = strtolower($arguments['category'][0]) ?? null;

        // Simple and trivial validation
        if ($error = $this->getValidationError($text, $category_name)) {
            $this->replyWithErrorMessage($error);

            return;
        }

        DB::transaction(function () use ($text, $category_name) {
            if ( ! $quote = $this->createNewQuote($text)) {
                throw new RuntimeException('Fallo al añadir quote.');
            }

            // Uncategorized quotes do not need to do anything else
            if (empty($category_name)) {
                return;
            }

            if ( ! $category = $this->getOrCreateCategory($category_name)) {
                throw new RuntimeException(
                    'Fallo al guardar categoría.'
                );
            }

            if ( ! $quote->categories()->save($category)) {
                throw new RuntimeException(
                    'Fallo al guardar relación de quote/categoría.'
                );
            }
        });

        $this->replyWithMessage([
            'text' => '✅ Quote agregado... ¡y lo llevo aquí colgado!',
        ]);
    }
}