<?php

namespace Webcomcafe\Pix\Resources;

/**
 * Classe para criação de cobranças com código copia e cola e geração de qrcode
 *
 * @author Airton Lopes <webcomcafe@outlook.com>
 * @copyright 2022
 */
class CobEmv extends Cob
{
    /**
     * @var string $resourceUpdatePath
     */
    protected $resourceUpdatePath = '/cob-emv/{txid}';

    /**
     * @var string $resourceCreatePath
     */
    protected $resourceCreatePath = '/cob-emv/{txid}';
}