<?php

namespace Webcomcafe\Pix\Facades;

use Webcomcafe\Pix\Resources\Resource;

/**
 * @method static create(array $data) Criar cobrança imediata.
 * @method static update(array $data) Criar cobrança imediata.
 */
class CobEmv extends Facade
{
    protected static function getApplication(): Resource
    {
        return new \Webcomcafe\Pix\Resources\CobEmv();
    }
}