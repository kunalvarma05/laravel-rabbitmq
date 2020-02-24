<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\PublishConfig;
use Kunnu\RabbitMQ\RabbitMQManager;
use Kunnu\RabbitMQ\RabbitMQMessage;
use PhpAmqpLib\Message\AMQPMessage;
use Kunnu\RabbitMQ\RabbitMQExchange;
use Kunnu\RabbitMQ\RabbitMQException;
use Kunnu\RabbitMQ\RabbitMQPublisher;

class RabbitMQPublisherTest extends TestCase
{
    public function testCanPublishMessage()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $publisher = $rabbitMQ->publisher();
        $this->assertInstanceOf(RabbitMQPublisher::class, $publisher);
        $publisher->publish(new RabbitMQMessage("Hello"));
    }

    public function testCanBulkPublishMessage()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $publisher = $rabbitMQ->publisher();
        $this->assertInstanceOf(RabbitMQPublisher::class, $publisher);
        $publisher->setMaxBatchSize(5);
        /**
         * @var RabbitMQMessage[]
         */
        $messages = array_map(fn ($message) => new RabbitMQMessage("Message {$message}"), range(1, 20));
        $messages[0]->setExchange(new RabbitMQExchange('test', ['declare' => true]));
        $publisher->publish($messages, '', null, new PublishConfig(
            ['channel_id' => 1],
            $rabbitMQ->resolveConfig($rabbitMQ->resolveDefaultConfigName())
        ));
    }

    public function testThrowsExceptionWhenNoMessagesAreGiven()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $publisher = $rabbitMQ->publisher();
        $this->assertInstanceOf(RabbitMQPublisher::class, $publisher);
        $messages = [];
        $this->expectException(RabbitMQException::class);
        $this->expectExceptionMessageMatches("/No messages/");
        $publisher->publish($messages);
    }

    public function testReturnsAmqpMessage()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $publisher = $rabbitMQ->publisher();
        $this->assertInstanceOf(RabbitMQPublisher::class, $publisher);
        $message = new RabbitMQMessage('Test');
        $this->assertInstanceOf(AMQPMessage::class, $message->getAmqpMessage());
        $publisher->publish($message);
    }
}
