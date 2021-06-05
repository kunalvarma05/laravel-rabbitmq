<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\ConsumeConfig;
use Kunnu\RabbitMQ\RabbitMQQueue;
use Kunnu\RabbitMQ\RabbitMQManager;
use Kunnu\RabbitMQ\RabbitMQMessage;
use Kunnu\RabbitMQ\RabbitMQConsumer;
use Kunnu\RabbitMQ\RabbitMQExchange;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Kunnu\RabbitMQ\RabbitMQIncomingMessage;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Kunnu\RabbitMQ\RabbitMQGenericMessageConsumer;

class RabbitMQConsumerTest extends TestCase
{
    public function testCanConsumeMessage()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $consumer = $rabbitMQ->consumer();
        $routingKey = 'key';

        $this->assertInstanceOf(RabbitMQConsumer::class, $consumer);

        $exchange = new RabbitMQExchange('unit_test', ['declare' => true, 'durable' => true]);
        $queue = new RabbitMQQueue('my_queue', ['declare' => true, 'durable' => true]);

        // Need this beforehand to make sure the queue can hold data before we start consuming
        $rabbitMQ->resolveChannel()->exchange_declare($exchange->getName(), AMQPExchangeType::DIRECT, false, true, false);
        $rabbitMQ->resolveChannel()->queue_declare($queue->getName(), false, true, false, false);
        $rabbitMQ->resolveChannel()->queue_bind($queue->getName(), $exchange->getName(), $routingKey);

        $msg = new RabbitMQMessage('test');
        $msg->setExchange($exchange);

        $rabbitMQ->publisher()->publish(
            $msg,
            $routingKey
        );

        $messageConsumer = new RabbitMQGenericMessageConsumer(
            function (RabbitMQIncomingMessage $message) {
                $this->assertEquals('test', $message->getStream());
                $message->getDelivery()->getConfig()->put('body', 'quit');
                $message->getDelivery()->acknowledge();

                return false;
            },
            $this,
        );

        $messageConsumer
            ->setExchange($exchange)
            ->setQueue($queue);

        $consumer->consume(
            $messageConsumer,
            $routingKey,
            null,
            new ConsumeConfig(
                [
                    'queue' => [
                        'name' => 'my_queue',
                        'declare' => true,
                        'durable' => true,
                    ],
                    'exchange' => [
                        'name' => 'unit_test',
                        'declare' => true,
                    ],
                ],
                $rabbitMQ->resolveConfig($rabbitMQ->resolveDefaultConfigName()),
            )
        );
    }

    public function testCanConsumeMessagesWithConfig()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $consumer = $rabbitMQ->consumer();
        $routingKey = 'key';
        $queueName = 'my_queue';

        $this->assertInstanceOf(RabbitMQConsumer::class, $consumer);

        $exchange = new RabbitMQExchange('unit_test', ['declare' => true, 'durable' => true]);

        // Need this beforehand to make sure the queue can hold data before we start consuming
        $rabbitMQ->resolveChannel()->exchange_declare($exchange->getName(), AMQPExchangeType::DIRECT, false, true, false);
        $rabbitMQ->resolveChannel()->queue_declare($queueName, false, true, false, false);
        $rabbitMQ->resolveChannel()->queue_bind($queueName, $exchange->getName(), $routingKey);

        $msg = new RabbitMQMessage('test');
        $msg->setExchange($exchange);

        $rabbitMQ->publisher()->publish(
            $msg,
            $routingKey
        );

        $messageConsumer = new RabbitMQGenericMessageConsumer(
            function (RabbitMQIncomingMessage $message) {
                $this->assertEquals('test', $message->getStream());
                $message->getDelivery()->getConfig()->put('body', 'quit');
                $message->getDelivery()->acknowledge();

                return false;
            },
            $this,
        );

        $consumer->consume(
            $messageConsumer,
            $routingKey,
            null,
            new ConsumeConfig(
                [
                    'queue' => [
                        'name' => $queueName,
                        'declare' => true,
                        'durable' => true,
                    ],
                    'exchange' => [
                        'name' => 'unit_test',
                        'declare' => true,
                    ],
                    'qos' => [
                        'enabled' => true,
                    ],
                ],
                $rabbitMQ->resolveConfig($rabbitMQ->resolveDefaultConfigName()),
            )
        );
    }

    public function testCanConsumeMessagesFromFanout()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $consumer = $rabbitMQ->consumer();

        $this->assertInstanceOf(RabbitMQConsumer::class, $consumer);

        $exchange = new RabbitMQExchange(
            'fanout_exchange',
            [
                'durable' => true,
                'declare' => true,
                'type' => AMQPExchangeType::FANOUT,
            ]
        );

        $msg = new RabbitMQMessage('test');
        $msg->setExchange($exchange);

        // Need this beforehand to make sure the queue can hold data before we start consuming
        $rabbitMQ->resolveChannel()->exchange_declare($exchange->getName(), AMQPExchangeType::FANOUT, false, true, false);
        [$queueName,] = $rabbitMQ->resolveChannel()->queue_declare("", false, true, true, false);
        $rabbitMQ->resolveChannel()->queue_bind($queueName, $exchange->getName());

        $rabbitMQ->publisher()->publish($msg);

        $messageConsumer = new RabbitMQGenericMessageConsumer(
            function (RabbitMQIncomingMessage $message) {
                $this->assertEquals('test', $message->getStream());
                $message->getDelivery()->getConfig()->put('body', 'quit');
                $message->getDelivery()->acknowledge();

                return false;
            },
            $this,
        );

        $queue = new RabbitMQQueue($queueName);
        $messageConsumer->setExchange($exchange)->setQueue($queue);

        try {
            $consumer->consume(
                $messageConsumer,
                '',
                null,
                new ConsumeConfig(['wait_timeout' => 1, 'qos' => ['enabled' => true]])
            );
        } catch (AMQPTimeoutException $e) {
            // We can't test fanout exchanges in cases when
            // the queue name is generated randomly.
            //
            // And since the consumer starts a blocking loop,
            // we can't publish after starting the consumer.
        }
    }
}
