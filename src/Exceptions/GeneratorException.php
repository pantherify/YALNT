<?php


namespace Pantherify\YALNT\Exceptions;


class GeneratorException extends \Exception
{
    public static function FlowError(\Exception $exception)
    {
        return new self($exception->message, 300, $exception);
    }
}
