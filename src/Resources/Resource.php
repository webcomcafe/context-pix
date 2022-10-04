<?php

namespace Webcomcafe\Pix\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Webcomcafe\Pix\Exceptions\BadRequestException;
use Webcomcafe\Pix\SDK;

/**
 * Classe Resource que possui todos os métodos de acesso aos recursos da api pix
 *
 * @author Airton Lopes <webcomcafe@outlook.com>
 * @copyright 2022
 */
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
    protected $resourceCreatePath;

    /**
     * Path para listar recursos
     *
     * @var string $resourceAllPath
     */
    protected $resourceAllPath;

    /**
     * Path para buscar um recurso
     *
     * @var string $resourceFindPATH
     */
    protected $resourceFindPath;

    /**
     * Path para atualizar um recurso
     *
     * @var string $resourceUpdatePath
     */
    protected $resourceUpdatePath;

    /**
     * Caminho de mudança em um recurso
     *
     * @var string $resourceChangePath
     */
    protected $resourceChangePath;

    /**
     * Path para remover um recurso
     *
     * @var string $resourceRemovePath
     */
    protected $resourceRemovePath;

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
        $this->config();
    }

    /**
     * Configurando cliente de requisições HTTP à api do PSP
     *
     * @return void
     */
    private function config()
    {
        // Recuperando SDK
        $this->sdk = SDK::getInstance();

        $config = [
            'timeout' => 10.0,
            'base_uri' => $this->sdk->getPsp()->getBaseUrl(),
            'verify' => $this->sdk->getPsp()->getEnv(),
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Content-Type'  => 'application/json',
            ]
        ];

        if( $cert = $this->sdk->getPsp()->getCertificate() ) {
            $config['cert'] = $cert;
        }

        // Definindo cliente HTTP
        $this->api = new Client($config);

        // Obtendo paths do recurso
        foreach ($this->endpoints as $path => $actions) {
            foreach ($actions as $action) {
                $name = 'resource'.ucfirst($action).'Path';
                $this->$name = $this->$name ?? $path;
            }
        }
    }

    /**
     * Define credenciais de autenticação
     *
     * @param array $config
     * @throws BadRequestException
     */
    private function checkForCredentials(array &$config)
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

        $res = $this->request('POST', $psp->getAPIAuthenticationPath(), [
            'headers' => ['Authorization'=>$basic, 'Content-type'=>'application/x-www-form-urlencoded'],
            'form_params' => ['grant_type'=>$psp->getGrantType(), 'scope'=>$psp->getScope()]
        ]);

        $now = (new \DateTime)->getTimestamp();
        $token = "{$res->token_type} {$res->access_token}";
        $storage = sprintf('%s.%s/%s', $now, $res->expires_in, $token);
        $this->sdk->fire('after.auth', [$storage]);

        return $token;
    }

    /**
     * Prepara uma requisição
     *
     * @param string $verb
     * @param string $path
     * @param array $data
     * @return mixed|void
     * @throws BadRequestException
     */
    protected function make(string $verb, string $path = '', array $data = [])
    {
        $options = [];
        $this->checkForCredentials($options);

        if( !empty($data)) {

            foreach ($data as $name => $value)
            {
                // Capturando configurações de requisição
                if( substr($name, 0, 2) == '@' ) {
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

        return $this->request($verb, $this->getResourcePath($path), $options);
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
    private function request($method, $path, $options)
    {
        try {
            $res = $this->api->request($method, $path, $options);
            return $this->resolveResponse($res);
        } catch (\Throwable $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();

            if ($e instanceof  \GuzzleHttp\Exception\ClientException) {
                $e = $this->resolveResponse($e->getResponse());

                if( isset($e->status) ) {
                    $code = $e->status;
                    $msg = isset($e->violacoes) ? $e->violacoes[0]->razao : $e->detail ?? $e->title;
                }
            }

            throw new BadRequestException($msg, $code > 0 ? $code : 500);
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

        return json_decode($content);
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
        return $this->make('POST', $this->resourceCreatePath, $data);
    }

    /**
     * Listar recursos
     *
     * @param array $data
     * @return mixed
     */
    public function all(array $data = [])
    {
        return $this->make('GET', $this->resourceAllPath, $data);
    }

    /**
     * Busca por um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function find(array $data)
    {
        return $this->make('GET', $this->resourceFindPath, $data);
    }

    /**
     * Atualiza um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function update(array $data)
    {
        return $this->make('PUT', $this->resourceUpdatePath, $data);
    }

    /**
     * Altera uma informação de um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function change(array $data)
    {
        return $this->make('PATCH', $this->resourceChangePath, $data);
    }

    /**
     * Remove um recurso
     *
     * @param array $data
     * @return mixed
     */
    public function remove(array $data)
    {
        return $this->make('DELETE', $this->resourceRemovePath, $data);
    }
}