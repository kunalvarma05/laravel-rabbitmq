<?php

namespace Kunnu\RabbitMQ;

use Illuminate\Support\Collection;

class ConnectionConfig
{
    /**
     * Configuration.
     *
     * @var Collection $config
     */
    protected Collection $config;

    /**
     * Create a new ConnectionConfig instance.
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        $this->config = new Collection($config);
    }

    /**
     * Get connection host.
     *
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->config->get('host', '127.0.0.1');
    }

    /**
     * Get connection port.
     *
     * @return integer|null
     */
    public function getPort(): ?int
    {
        return $this->config->get('port', 5672);
    }

    /**
     * Get connection user.
     *
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->config->get('username', '');
    }

    /**
     * Get connection password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->config->get('password', '');
    }

    /**
     * Get connection vhost.
     *
     * @return string|null
     */
    public function getVhost(): ?string
    {
        return $this->config->get('vhost', '/');
    }

    /**
     * Get connection SSL options.
     *
     * @return array|null
     */
    public function getSSLOptions(): ?array
    {
        return $this->config->get('ssl_options', []);
    }

    /**
     * Get connection options.
     *
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->config->get('options', []);
    }

    /**
     * Get connection SSL protocol.
     *
     * @return string|null
     */
    public function getSSLProtocol(): ?string
    {
        return $this->config->get('ssl_protocol', 'ssl');
    }
}
