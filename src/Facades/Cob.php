<?php

namespace Webcomcafe\Pix\Facades;

use Webcomcafe\Pix\Resources\Resource;

/**
 * @method static create(array $data) Criar cobrança imediata.
 * @method static find(array $data) Consultar cobrança imediata.
 * @method static update(array $data) Criar cobrança imediata.
 * @method static change(array $data) Revisar cobrança imediata.
 * @method static all(array $data) Consultar lista de cobranças imediatas.
 */
class Cob extends Facade
{
    protected static function getApplication(): Resource
    {
        return new \Webcomcafe\Pix\Resources\Cob();
    }
}