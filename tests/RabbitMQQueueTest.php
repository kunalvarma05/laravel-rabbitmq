<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQQueue;

class RabbitMQQueueTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQQueue::class, new RabbitMQQueue('test'));
    }

    public function testReturnsName()
    {
        $queue = new RabbitMQQueue('test');
        $this->assertEquals('test', $queue->getName());
    }

    public function testReturnsConfig()
    {
        $queue = new RabbitMQQueue('test', ['key' => 'value']);
        $this->assertEquals('value', $queue->getConfig()->get('key'));
    }
}
