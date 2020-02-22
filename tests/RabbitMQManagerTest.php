<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQManager;

class RabbitMQManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQManager::class, new RabbitMQManager());
    }
}
