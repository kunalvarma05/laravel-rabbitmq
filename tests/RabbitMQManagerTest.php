<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQManager;
use Kunnu\RabbitMQ\ConnectionConfig;
use Kunnu\RabbitMQ\RabbitMQPublisher;
use Illuminate\Contracts\Container\Container;
use PhpAmqpLib\Connection\AbstractConnection;

class RabbitMQManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQManager::class, new RabbitMQManager($this->app));
    }

    public function testCanResolveConfiguration()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $config  = $rabbitMQ->resolveConfig(RabbitMQManager::class);
        $this->assertInstanceOf(ConnectionConfig::class, $config);
    }

    public function testCanResolveConnection()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $connection  = $rabbitMQ->resolveConnection();
        $this->assertInstanceOf(AbstractConnection::class, $connection);
    }

    public function testCanReturnResolvedConnections()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $rabbitMQ->getConfig()->set(
            RabbitMQManager::CONFIG_KEY . ".connections.rabbitmq2",
            $rabbitMQ->getConfig()->get(RabbitMQManager::CONFIG_KEY . ".connections.rabbitmq")
        );
        $connection  = $rabbitMQ->resolveConnection('rabbitmq');
        $connectionTwo  = $rabbitMQ->resolveConnection('rabbitmq2');
        $this->assertCount(2, $rabbitMQ->getConnections());
    }

    public function testCanReturnResolvedChannels()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $connection  = $rabbitMQ->resolveConnection();
        $channel = $rabbitMQ->resolveChannel();
        $channel = $rabbitMQ->resolveChannel(null, 2);
        $this->assertCount(2, $rabbitMQ->getChannels());
    }

    public function testCanReturnApplicationContainer()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $this->assertInstanceOf(Container::class, $rabbitMQ->getApp());
    }

    public function testCanMakePublisher()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $publisher = $rabbitMQ->publisher();
        $this->assertInstanceOf(RabbitMQPublisher::class, $publisher);
    }
}
