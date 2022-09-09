<?php

namespace Webcomcafe\Pix\Psp;

interface PspInterface
{
    /**
     * Definir o ambiente como teste
     *
     * @param bool $env
     * @return mixed
     */
    public function setAsTest(bool $env): PspInterface;

    /**
     * ClientId obtido junto ao PSP
     *
     * @param string $clientId
     * @return mixed
     */
    public function setClientId(string $clientId): PspInterface;

    /**
     * Retorna o tipo de permissão
     *
     * @return string
     */
    public function getGrantType(): string;

    /**
     * Rettorna o escopo
     *
     * @return string
     */
    public function getScope(): string;

    /**
     * ClientSecret obtido junto ao PSP
     *
     * @param string $clientSecret
     * @return mixed
     */
    public function setClientSecret(string $clientSecret): PspInterface;

    /**
     * Retorna client_secret
     *
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * Retorna client_id
     * @return string
     */
    public function getClientId(): string;

    /**
     * Token de authorização, caso já tenha sido gerado
     *
     * @param string $basicToken
     * @return mixed
     */
    public function setAuthorizationToken(string $basicToken): PspInterface;

    /**
     * Retorna o token de autorização
     *
     * @return string
     */
    public function getAuthorizationToken(): string;

    /**
     * Versão da api
     *
     * @param string $version
     * @return mixed
     */
    public function setAPIVersion(string $version): PspInterface;

    /**
     * Caminho para autenticação
     *
     * @param string $authenticatePath
     * @return mixed
     */
    public function setAPIAuthenticationPath(string $authenticatePath): PspInterface;

    /**
     * Chave PIX gerada na instrução financeira (PSP)
     *
     * @param string $keyPix
     * @return mixed
     */
    public function setKeyPix(string $keyPix): PspInterface;

    /**
     * Certificado digial
     *
     * @param string $pathToFile
     * @param string $pwd
     * @return mixed
     */
    public function setCertificate(string $pathToFile, string $pwd): PspInterface;

    /**
     * Retorna o path de autenticação do PSP
     *
     * @return string
     */
    public function getAPIAuthenticationPath(): string;

    /**
     * Retorna URL de produção ou homologação, dependendo da propriedade $test
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Retorna o certificado digital
     *
     * @return array
     */
    public function getCertificate(): array;


}