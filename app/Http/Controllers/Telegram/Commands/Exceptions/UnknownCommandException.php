<?php


namespace App\Http\Controllers\Telegram\Commands\Exceptions;


/**
 * Thrown when an unrecognized command reaches the application's command
 * handling.
 *
 * @package App\Http\Controllers\Telegram\Commands\Exceptions
 */
final class UnknownCommandException extends \RuntimeException
{

}