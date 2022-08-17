<?php

namespace Kunnu\RabbitMQ;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use Illuminate\Contracts\Container\Container;
use PhpAmqpLib\Connection\AbstractConnection;

class RabbitMQManager
{
    /**
     * Configuration key.
     *
     * @var string
     */
    const CONFIG_KEY = 'rabbitmq';

    /**
     * IoC Container/Application.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Configuration repository.
     *
     * @var Repository
     */
    protected Repository $config;

    /**
     * Connection pool.
     *
     * @var Collection
     */
    protected Collection $connections;

    /**
     * Channel pool.
     *
     * @var Collection
     */
    protected Collection $channels;

    /**
     * Create a new RabbitMQManager instance.
     *
     * @param  Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->config = $this->app->get('config');
        $this->connections = new Collection([]);
        $this->channels = new Collection([]);
    }

    /**
     * Get Connections.
     *
     * @return Collection
     */
    public function getConnections(): Collection
    {
        return $this->connections;
    }

    /**
     * Get Config.
     *
     * @return Repository
     */
    public function getConfig(): Repository
    {
        return $this->config;
    }

    /**
     * Get Application Container.
     *
     * @return Container
     */
    public function getApp(): Container
    {
        return $this->app;
    }

    /**
     * Get Channels.
     *
     * @return Collection
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * Resolve default connection name.
     *
     * @return string
     */
    public function resolveDefaultConfigName(): string
    {
        $configKey = self::CONFIG_KEY;

        return $this->config->get("{$configKey}.defaultConnection", 'rabbitmq');
    }

    /**
     * Resolve connection instance by name.
     *
     * @param  string|null  $name
     * @param  ConnectionConfig|null  $config
     * @return AbstractConnection
     */
    public function resolveConnection(?string $name = null, ?ConnectionConfig $config = null): AbstractConnection
    {
        $name = $name ?? $this->resolveDefaultConfigName();

        if (!$this->connections->has($name)) {
            $this->connections->put(
                $name,
                $this->makeConnection($config ?? $this->resolveConfig($name))
            );
        }

        return $this->connections->get($name);
    }

    /**
     * Resolve connection configuration.
     *
     * @return ConnectionConfig
     */
    public function resolveConfig(string $connectionName): ConnectionConfig
    {
        $configKey = self::CONFIG_KEY;
        $connectionKey = "{$configKey}.connections.{$connectionName}";

        return new ConnectionConfig($this->config->get($connectionKey, []));
    }

    /**
     * Get the publisher.
     *
     * @return RabbitMQPublisher
     */
    public function publisher(): RabbitMQPublisher
    {
        return new RabbitMQPublisher($this);
    }

    /**
     * Get the consumer.
     *
     * @return RabbitMQConsumer
     */
    public function consumer(): RabbitMQConsumer
    {
        return new RabbitMQConsumer($this);
    }

    /**
     * Resolve the channel ID.
     *
     * @param  int|null  $channelId
     * @return int|null
     */
    public function resolveChannelId(?int $channelId, ?string $connectionName = null): ?int
    {
        $configKey = self::CONFIG_KEY;
        $connectionName = $connectionName ?? $this->resolveDefaultConfigName();
        // Use channel ID from the connection config if channel ID is not given
        $channelId = $channelId ?? $this->config->get("{$configKey}.{$connectionName}.channel_id");
        // else, use the default channel ID
        return $channelId ?? $this->config->get("{$configKey}.defaults.channel_id", $channelId);
    }

    /**
     * Resolve channel for the given connection.
     *
     * @param  string|null  $connectionName
     * @param  int|null  $channelId
     * @param  AbstractConnection|null  $connection
     * @return AMQPChannel|null
     */
    public function resolveChannel(
        ?string $connectionName = null,
        ?int $channelId = null,
        ?AbstractConnection $connection = null
    ): AMQPChannel {
        if (!$connection) {
            $connection = $this->resolveConnection($connectionName);
        }

        $channelId = $channelId ?? $this->resolveChannelId($channelId);

        if (!$this->channels->has("{$connectionName}.{$channelId}")) {
            $this->channels->put("{$connectionName}.{$channelId}", $connection->channel($channelId));
        }

        return $this->channels->get("{$connectionName}.{$channelId}");
    }

    /**
     * Create a new connection.
     *
     * @param  ConnectionConfig  $config
     * @return AbstractConnection
     */
    protected function makeConnection(ConnectionConfig $config): AbstractConnection
    {
        return new AMQPSSLConnection(
            $config->getHost(),
            $config->getPort(),
            $config->getUser(),
            $config->getPassword(),
            $config->getVhost(),
            $config->getSSLOptions(),
            $config->getOptions(),
            $config->getSSLProtocol(),
        );
    }
}
