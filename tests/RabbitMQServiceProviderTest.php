<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQManager;
use Kunnu\RabbitMQ\RabbitMQServiceProvider;

class RabbitMQServiceProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQServiceProvider::class, new RabbitMQServiceProvider($this->app));
    }

    public function testItRegistersASingleton()
    {
        $singleton = $this->app->getBindings()[RabbitMQManager::class]['concrete']($this->app);
        $this->assertInstanceOf(RabbitMQManager::class, $singleton);
        $this->assertTrue($this->app->bound(RabbitMQManager::class));
    }

    public function testItRegistersABinding()
    {
        $provider = new RabbitMQServiceProvider($this->app);
        $provider->register();
        $binding = $this->app->getBindings()['rabbitmq']['concrete']($this->app);
        $this->assertInstanceOf(RabbitMQManager::class, $binding);
        $this->assertContains('rabbitmq', $provider->provides());
        $this->assertTrue($this->app->bound('rabbitmq'));
    }
}
