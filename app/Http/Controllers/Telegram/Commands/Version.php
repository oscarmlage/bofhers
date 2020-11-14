<?php


namespace App\Http\Controllers\Telegram\Commands;

/**
 * Display the codebase's version.
 *
 * @package App\Http\Controllers\Telegram\Commands
 */
final class Version extends AbstractCommand
{

    protected $hidden = true;
    protected $name = 'version';
    protected $description = 'Muestra la versión del bot y herramienta.';

    public function handlerBofhers(array $arguments = null)
    {
        if ( ! $this->isValidChannel()) {
            return;
        }

        $version = file_get_contents(
            base_path() . '/VERSION', true
        );

        if ($version === false) {
            $this->replyWithErrorMessage(
                'No tengo ni idea. O mejor dicho: algo ha petao. ' .
                'Mete un par de "dds()" para depurar el código o ' .
                'abre puertos en el router para meter XDebug y ver ' .
                'qué pasa.',
            );

            return;
        }

        $this->answerWithMessage("Versión ${version}");
    }
}