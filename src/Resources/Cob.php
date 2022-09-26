<?php

namespace Webcomcafe\Pix\Resources;

use Webcomcafe\Pix\Exceptions\InvalidImplementException;

/**
 * Classe Cob Implementa um recurso de cobrança pix
 *
 * @author Airton Lopes <webcocmafe@outlook.com>
 */
class Cob extends Resource
{
    /**
     * Endpoints do recurso
     *
     * @var \string[][]
     */
    protected $endpoints = [
        '/cob/{txid}' => ['find', 'update', 'change', 'remove'],
        '/cob' => ['create', 'all']
    ];

    /**
     * @param array $data
     * @return mixed
     * @throws InvalidImplementException
     */
    public function remove(array $data)
    {
        throw new InvalidImplementException('O método Cob::delete não existe no contexto pix');
    }
}