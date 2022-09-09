<?php

namespace Webcomcafe\Pix\Facades;

use Webcomcafe\Pix\Resources\Resource;

abstract class Facade
{
    /**
     * Instãncia de resorce a ser executada
     *
     * @return Resource
     */
    abstract protected static function getApplication(): Resource;

    /**
     * Operação a ser chamada
     *
     * @param $name
     * @param $arguments
     * @return void
     */
    final public static function __callStatic($name, $arguments)
    {
        $app = static::getApplication();

        return $app->{$name}(...$arguments);
    }
}