<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQManager;
use Kunnu\RabbitMQ\ConnectionConfig;
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
        $connection->close();
    }
}
