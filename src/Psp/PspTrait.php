<?php

namespace Webcomcafe\Pix\Psp;

/**
 * @mixin Psp
 */

trait PspTrait
{
    /**
     * client_id
     *
     * @var string $clientId
     */
    protected $clientId;

    /**
     * @var string $clientSecret
     */
    protected $clientSecret;

    /**
     * Tipo de permissão
     *
     * @var string $grantType
     */
    protected $grantType;

    /**
     * Escopos de acesso
     *
     * @var string $scope
     */
    protected $scope = '';

    /**
     * Path de autenticação
     *
     * @var string $authenticatePath
     */
    protected $authenticatePath;

    /**
     * URL de produção
     *
     * @var string $baseUrl
     */
    protected $baseUrl;

    /**
     * URl de homologação
     *
     * @var string $baseUrlH
     */
    protected $baseUrlH;

    /**
     * Versão da API
     *
     * @var string $version
     */
    protected $version;

    /**
     * Campo que representa o txid
     *
     * @var string $txKey
     */
    protected $txKey;

    /**
     * Ambiente de teste
     *
     * @var bool $test
     */
    protected $test = false;

    /**
     * @var string $authorizationToken
     */
    protected $authorizationToken;

    /**
     * Chave Pix para recebimento
     *
     * @var string $keyPix
     */
    protected $keyPix;

    /**
     * Certificado digital e senha
     *
     * [path/to/cert, password]
     *
     * @var array $certificate
     */
    protected $certificate = [];

    /**
     * Define o ambiente como homologação para teste
     *
     * @param bool $env
     * @return void
     */
    public function setAsTest(bool $env): PspInterface
    {
        $this->test = $env;

        return $this;
    }

    /**
     * Retorna URL de produção ou homologação
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        $baseUrl = !$this->test ? $this->baseUrl : $this->baseUrlH;

        return trim($baseUrl,'/');
    }

    /**
     * ClientId obtido junto ao PSP
     *
     * @param string $clientId
     * @return PspInterface
     */
    public function setClientId(string $clientId): PspInterface
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Retorna client_id
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Tipo de permissão
     *
     * @return string
     */
    public function getGrantType(): string
    {
        return $this->grantType;
    }

    /**
     * Escopo de operação
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * ClienteSecret obtido junto ao PSP
     *
     * @param string $clientSecret
     * @return PspInterface
     */
    public function setClientSecret(string $clientSecret): PspInterface
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Retorna client secret
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Definindo path de autenticação
     *
     * @param string $authenticatePath
     * @return PspInterface
     */
    public function setAPIAuthenticationPath(string $authenticatePath): PspInterface
    {
        $this->authenticatePath = $authenticatePath;

        return $this;
    }

    /**
     * Retorna path para autenticação
     *
     * @return string
     */
    final public function getAPIAuthenticationPath(): string
    {
        return $this->authenticatePath;
    }

    /**
     * Definindo a versão da API
     *
     * @param string $version
     * @return PspInterface
     */
    public function setAPIVersion(string $version): PspInterface
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Retorna o nome do campo que representa o txid no PSP
     *
     * @return string
     */
    public function getTxKey(): string
    {
        return $this->txKey;
    }

    /**
     * Token de autorização
     *
     * @param string $basicToken
     * @return PspInterface
     */
    public function setAuthorizationToken(string $basicToken): PspInterface
    {
        $this->authorizationToken = $basicToken;

        return $this;
    }

    /**
     * Retorna o token de autorização
     *
     * @return string
     */
    final public function getAuthorizationToken(): string
    {
        return $this->authorizationToken ?? '';
    }

    /**
     * Chave pix
     *
     * @param string $keyPix
     * @return PspInterface
     */
    public function setKeyPix(string $keyPix): PspInterface
    {
        $this->keyPix = $keyPix;

        return $this;
    }

    /**
     * Certificado digital e senha
     *
     * [path/to/certificate, password]
     *
     * @param string $pathToFile
     * @param string $pwd
     * @return PspInterface
     */
    public function setCertificate(string $pathToFile, string $pwd): PspInterface
    {
        $this->certificate = [$pathToFile, $pwd];

        return $this;
    }

    /**
     * Retorna o certificado
     *
     * @return array
     */
    public function getCertificate(): array
    {
        return $this->certificate;
    }
}