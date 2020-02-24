<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQQueue;
use PhpAmqpLib\Message\AMQPMessage;
use Kunnu\RabbitMQ\RabbitMQDelivery;
use Kunnu\RabbitMQ\RabbitMQExchange;
use Kunnu\RabbitMQ\RabbitMQException;
use Kunnu\RabbitMQ\RabbitMQIncomingMessage;

class RabbitMQIncomingMessageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQIncomingMessage::class, new RabbitMQIncomingMessage());
    }

    public function testGettersAndSettersWork()
    {
        $message = new RabbitMQIncomingMessage();
        // Setters
        $message
            ->setStream('test')
            ->setConsumer(null)
            ->setConfig(['key' => 'value'])
            ->setAmqpMessage(new AMQPMessage('test'))
            ->setQueue(new RabbitMQQueue('test'))
            ->setDelivery(new RabbitMQDelivery(['key' => 'value']))
            ->setExchange(new RabbitMQExchange('test'));

        // Getters
        $this->assertEquals('test', $message->getStream());
        $this->assertEquals(null, $message->getConsumer());
        $this->assertEquals('test', $message->getAmqpMessage()->body);
        $this->assertEquals('test', $message->getQueue()->getName());
        $this->assertEquals('value', $message->getDelivery()->getConfig()->get('key'));
        $this->assertEquals('test', $message->getExchange()->getName());
        $this->assertEquals('value', $message->getConfig()->get('key'));
        $this->assertEquals('value', $message->getMessageApplicationHeader('key', 'value'));

        $message->getDelivery()->getConfig()->put('delivery_info', ['redelivered' => true]);
        $this->assertTrue($message->isRedelivered());

        $message->getDelivery()->getConfig()->put('delivery_info', ['']);
        $this->assertFalse($message->isRedelivered());

        $message->setDelivery(null);
        $this->expectException(RabbitMQException::class);
        $message->isRedelivered();
    }
}
