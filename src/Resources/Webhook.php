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
        '/webhook/{chave}' => ['update', 'find', 'remove'],
        '/webhook' => ['all']
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

    /**
     * @param array $data
     * @return mixed|\stdClass|void
     * @throws InvalidImplementException
     */
    public function create(array $data)
    {
        throw new InvalidImplementException('O método Webhook::create não existe no contexto pix');
    }
}