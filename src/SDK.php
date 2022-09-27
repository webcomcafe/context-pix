<?php

namespace Webcomcafe\Pix;

use Webcomcafe\Pix\Psp\PspInterface;

class SDK
{
    /**
     * @var PspInterface $psp
     */
    private $psp;

    /**
     * @var SDK $instance;
     */
    private static $instance;

    /**
     * Eventos
     *
     * @var array $events
     */
    private $events = [
        'after.auth' => []
    ];


    /**
     * @param PspInterface $psp
     */
    public function __construct(PspInterface $psp)
    {
        $this->psp = $psp;
    }

    /**
     * @return PspInterface
     */
    public function getPsp(): PspInterface
    {
        return $this->psp;
    }

    /**
     * @return void
     */
    public function seAsGlobal()
    {
        SDK::$instance = $this;
    }

    /**
     * @return SDK
     */
    public static function getInstance():SDK
    {
        return self::$instance;
    }

    /**
     * Definindo um evento
     *
     * @param string $event
     * @param callable $callback
     * @return void
     */
    public function on(string $event, callable $callback)
    {
        $this->events[$event][] = $callback;
    }

    /**
     * Dispara um evento
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public function fire(string $event, array $data)
    {
        foreach ($this->events[$event] as $callback) {
            if( is_array($callback)) {
                list($class, $method) = $callback;
                (new $class)->{$method}(...$data);
            } else {
                $callback(...$data);
            }
        }
    }
}