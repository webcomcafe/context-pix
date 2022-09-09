<?php

namespace Webcomcafe\Pix\Resources;

use Webcomcafe\Pix\Exceptions\InvalidImplementException;

/**
 * Classe Webhook implementa um recurso Webhook nno pix
 *
 * @author Airton Lopes <webcocmafe@outlook.com>
 */
class Webhook extends Resource
{
    /**
     * Endpoints do recurso
     *
     * @var \string[][]
     */
    protected $endpoints = [
        '/{chave}' => ['find','update','remove']
    ];

    /**
     * @param array $data
     * @return mixed
     * @throws InvalidImplementException
     */
    public function change(array $data)
    {
        throw new InvalidImplementException('O método Webhook::change não existe no contexto pix');
    }
}