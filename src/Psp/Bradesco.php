<?php

namespace Webcomcafe\Pix\Psp;

/**
 *
 * Define informações do PSP Bradesco
 *
 * @author Airton Lopes <airtonlopes_@hotmail.com>
 *
 */
class Bradesco extends Psp
{
    /**
     * Tipo de permissão
     *
     * @var string $grantType
     */
    protected $grantType = 'client_credentials';

    /**
     * URL para uso em produção
     *
     * @var string $baseUrl
     */
    protected $baseUrl = 'https://qrpix.bradesco.com.br';

    /**
     * URL para uso em homologação
     *
     * @var string $baseUrlH
     */
    protected $baseUrlH = 'https://qrpix-h.bradesco.com.br';

    /**
     * Path para realizar a autenticação e obter token de autorização
     *
     * @var string $authenticatePath
     */
    protected $authenticatePath = '/auth/server/oauth/token';

    /**
     * Versão virgente da API
     *
     * @var string $version
     */
    protected $version = '/v2';
}