<?php

namespace Webcomcafe\Pix\Facades;

use Webcomcafe\Pix\Resources\Resource;

/**
 * @method static create(array $data)
 */
class Cob extends Facade
{
    protected static function getApplication(): Resource
    {
        return new \Webcomcafe\Pix\Resources\Cob();
    }
}