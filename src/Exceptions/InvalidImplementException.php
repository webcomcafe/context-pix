<?php

namespace Webcomcafe\Pix\Exceptions;

class InvalidImplementException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 500;

    /**
     * @var string
     */
    protected $message = 'A implementação deste método não consta na API pix';
}