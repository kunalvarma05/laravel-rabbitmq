<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQ;

class RabbitMQTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQ::class, new RabbitMQ());
        RabbitMQ::shouldReceive('get')->once()->andReturn('rabbitmq');
        RabbitMQ::get();
    }
}
