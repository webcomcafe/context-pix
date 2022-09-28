<?php

namespace Webcomcafe\Pix\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Webcomcafe\Pix\Exceptions\BadRequestException;
use Webcomcafe\Pix\SDK;

abstract class Resource implements ResourceInterface
{
    /**
     * @var SDK $sdk
     */
    protected $sdk;

    /**
     * @var Client $api
     */
    protected $api;

    /**
     * Path para criar recurso
     *
     * @var string $resourceCreatePATH
     */
    protected $resourceCreatePath = '';

    /**
     * Path para listar recursos
     *
     * @var string $resourceAllPath
     */
    protected $resourceAllPath = '';

    /**
     * Path para buscar um recurso
     *
     * @var string $resourceFindPATH
     */
    protected $resourceFindPath = '';

    /**
     * Path para atualizar um recurso
     *
     * @var string $resourceUpdatePath
     */
    protected $resourceUpdatePath = '';

    /**
     * Caminho de mudança em um recurso
     *
     * @var string $resourceChangePath
     */
    protected $resourceChangePath = '';

    /**
     * Path para remover um recurso
     *
     * @var string $resourceRemovePath
     */
    protected $resourceRemovePath = '';

    /**
     * Endpoints para realização de cada operação
     *
     * @var array $endpoints
     */
    protected $endpoints = [];

    /**
     * Configurando
     */
    final public function __construct()
    {
        $this->boot();
    }

    /**
     * Configurando cliente de requisições HTTP à api do PSP
     *
     * @return void
     */
    private function boot()
    {
        // Recuperando SDK
        $this->sdk = SDK::getInstance();

        // Definindo cliente HTTP
        $this->api = new Client([
            'timeout' => 10.0,
            'base_uri' => $this->sdk->getPsp()->getBaseUrl(),
            'verify' => $this->sdk->getPsp()->getEnv(),
            'cert' => $this->sdk->getPsp()->getCertificate(),
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Content-Type'  => 'application/json',
            ]
        ]);

        // Obtendo paths do recurso
        foreach ($this->endpoints as $path => $actions) {
            foreach ($actions as $action) {
                $this->{'resource'.ucfirst($action).'Path'} = $path;
            }
        }
    }

    /**
     * Define credenciais de autenticação
     *
     * @param array $config
     * @throws BadRequestException
     */
    private function setCredentials(array &$config)
    {
        $token = $this->sdk->getPsp()->getAuthorizationToken();

        if( !$token || !$this->checkForAccessToken($token)) {
            $token = $this->authenticate();
        }

        $config['headers']['Authorization'] = $token;
    }

    /**
     * Retorna se um token é válido
     *
     * @param string $accessToken
     * @return bool
     */
    private function checkForAccessToken(string &$accessToken): bool
    {
        list($expires, $accessToken) = explode('/', $accessToken);
        list($timestamp, $timer) = explode('.', $expires);

        $now = new \DateTime;
        $end = new \DateTime;
        $end->setTimestamp($timestamp)->modify("+{$timer}seconds");

        // Se a data atual($now) for maior que a data de expiração($end), então
        // o token expirou, caso contrário é válido
        return $now < $end;
    }

    /**
     * Realiza um processo de autenticação e obtenção do access token
     *
     * @return string
     * @throws BadRequestException
     */
    private function authenticate(): string
    {
        $psp = $this->sdk->getPsp();
        $basic = 'Basic '.base64_encode($psp->getClientId().':'.$psp->getClientSecret());

        $obj = $this->dispatch('POST', $psp->getAPIAuthenticationPath(), [
            'headers' => ['Authorization'=>$basic, 'Content-type'=>'application/x-www-form-urlencoded'],
            'form_params' => ['grant_type'=>$psp->getGrantType(), 'scope'=>$psp->getScope()]
        ]);

        $timestamp = (new \DateTime)->getTimestamp();
        $token = sprintf('%s.%s/%s %s', $timestamp, $obj->expires_in, $obj->token_type, $obj->access_token);
        $this->sdk->fire('after.auth', [$token]);

        return "{$obj->token_type} {$obj->access_token}";
    }

    /**
     * Prepara uma requisição
     *
     * @param string $verb
     * @param string $path
     * @param array $data
     * @return mixed
     */
    protected function req(string $verb, string $path = '', array $data = [])
    {
        $options = [];
        $this->setCredentials($options);

        if( !empty($data)) {

            foreach ($data as $name => $value)
            {
                // Capturando configurações de requisição
                if( substr($name, 0, 2) == '::' ) {
                    $options[$name] = $value;
                    unset($data[$name]);
                    continue;
                }

                // Definindo parâmetros de path
                if( strpos($name,':') !== false ) {
                    $path = str_replace('{'.substr($name, 1).'}', $value, $path);
                    unset($data[$name]);
                }

                // Definindo parâmetros de query
                if( strpos($name,'?') !== false ) {
                    $options['query'][substr($name,1)] = $value;
                    unset($data[$name]);
                }
            }

            $options['body'] = json_encode($data);
        }

        return $this->dispatch($verb, $this->getResourcePath($path), $options);
    }

    /**
     * Realiza uma requisição
     *
     * @param $method
     * @param $path
     * @param $options
     * @return mixed|void
     * @throws BadRequestException
     */
    private function dispatch($method, $path, $options)
    {
        try {
            $res = $this->api->request($method, $path, $options);
            return $this->resolveResponse($res);
        } catch (\Throwable $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();

            if ($e instanceof  \GuzzleHttp\Exception\ClientException) {
                $err = $this->resolveResponse($e->getResponse());

                if( isset($err->status) ) {
                    $error = isset($err->violacoes) ? $err->violacoes[0]->razao : $err->detail;
                    $msg = $error ?? $err->error;
                    $code = $err->status;
                }
            }

            if( isset($err->error) ) {
                throw new BadRequestException($msg, $code);
            }
        }
    }


    /**
     * Converte uma resposta JSON para objeto
     *
     * @param Response $response
     * @return mixed
     */
    private function resolveResponse(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();

        $result = json_decode($content);

        if( $response->getStatusCode() > 300 ) {
            $result->error = true;
        }

        return $result;
    }

    /**
     * Retorna o path do recurso
     *
     * @param string $resourceBasePath
     * @return string
     */
    protected function getResourcePath(string $resourceBasePath): string
    {
        $version = $this->sdk->getPsp()->getAPIVersion();

        return strtolower($version.$resourceBasePath);
    }

    /**
     * Cria um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->req('POST', $this->resourceCreatePath, $data);
    }

    /**
     * Listar recursos
     *
     * @param array $data
     * @return mixed
     */
    public function all(array $data = [])
    {
        return $this->req('GET', $this->resourceAllPath, $data);
    }

    /**
     * Busca por um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function find(array $data)
    {
        return $this->req('GET', $this->resourceFindPath, $data);
    }

    /**
     * Atualiza um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function update(array $data)
    {
        return $this->req('PUT', $this->resourceUpdatePath, $data);
    }

    /**
     * Altera uma informação de um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function change(array $data)
    {
        return $this->req('PATCH', $this->resourceChangePath, $data);
    }

    /**
     * Remove um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function remove(array $data)
    {
        return $this->req('DELETE', $this->resourceRemovePath, $data);
    }
}