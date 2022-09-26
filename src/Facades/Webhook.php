<?php

namespace Webcomcafe\Pix\Facades;

use Webcomcafe\Pix\Resources\Resource;

/**
 * @method static update(array $data) Configurar o Webhook Pix.
 * @method static find(array $data) Exibir informações acerca do Webhook Pix.
 * @method static remove(array $data) Cancelar o webhook Pix.
 * @method static all(array $data = []) Consultar webhooks cadastrados.
 */
class Webhook extends Facade
{
    protected static function getApplication(): Resource
    {
        return new \Webcomcafe\Pix\Resources\Webhook();
    }
}