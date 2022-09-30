<?php

namespace Webcomcafe\Pix\Resources;

use Webcomcafe\Pix\Exceptions\InvalidImplementException;

/**
 * Reúne métodos destinados a lidar com gerenciamento de cobranças imediatas
 *
 * @author Airton Lopes <webcocmafe@outlook.com>
 * @copyright 2022
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