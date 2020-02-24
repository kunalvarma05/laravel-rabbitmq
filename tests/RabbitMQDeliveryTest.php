<?php

namespace Kunnu\RabbitMQ\Test;

use PhpAmqpLib\Channel\AMQPChannel;
use Kunnu\RabbitMQ\RabbitMQDelivery;
use Kunnu\RabbitMQ\RabbitMQException;

class RabbitMQDeliveryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIsDefined()
    {
        $this->assertInstanceOf(RabbitMQDelivery::class, new RabbitMQDelivery());
    }

    public function testItStoresConfig()
    {
        $delivery = new RabbitMQDelivery(['key' => 'value']);
        $this->assertEquals('value', $delivery->getConfig()->get('key'));
    }

    public function testItCanAcknowledgeAMessage()
    {
        $mockChannel = $this->createStub(AMQPChannel::class);
        $mockChannel->method('basic_ack')->willReturn(true);
        $delivery = new RabbitMQDelivery([
            'delivery_info' => ['channel' => $mockChannel],
        ]);
        $this->assertTrue($delivery->acknowledge());
    }

    public function testItCanThrowExceptionWhenAcknowledgingAMessage()
    {
        $mockChannel = $this->createStub(AMQPChannel::class);
        $mockChannel->method('basic_ack')->willReturn(true);
        $delivery = new RabbitMQDelivery();
        $this->expectException(RabbitMQException::class);
        $delivery->acknowledge();
    }

    public function testItCanCancelAMessage()
    {
        $mockChannel = $this->createStub(AMQPChannel::class);
        $mockChannel->method('basic_cancel')->willReturn(true);
        $delivery = new RabbitMQDelivery([
            'delivery_info' => ['channel' => $mockChannel],
            'body' => 'quit',
        ]);
        $this->assertTrue($delivery->acknowledge());
    }

    public function testItCanRejectAMessage()
    {
        $mockChannel = $this->createStub(AMQPChannel::class);
        $mockChannel->method('basic_reject')->willReturn(true);
        $delivery = new RabbitMQDelivery([
            'delivery_info' => ['channel' => $mockChannel],
        ]);
        $this->assertTrue($delivery->reject());
    }

    public function testItCanThrowExceptionWhenRejectingAMessage()
    {
        $mockChannel = $this->createStub(AMQPChannel::class);
        $mockChannel->method('basic_reject')->willReturn(true);
        $delivery = new RabbitMQDelivery();
        $this->expectException(RabbitMQException::class);
        $delivery->reject();
    }
}
