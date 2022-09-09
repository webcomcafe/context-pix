<?php

namespace Webcomcafe\Pix\Exceptions;

class BadRequestException extends \Exception
{
    protected $message = 'Requisição inválida';

    protected $code = 400;
}