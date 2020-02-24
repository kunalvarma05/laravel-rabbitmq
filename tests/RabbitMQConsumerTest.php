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

        $this->assertInstanceOf(RabbitMQConsumer::class, $consumer);

        $exchange = new RabbitMQExchange('unit_test', ['declare' => true, 'durable' => true]);
        $queue = new RabbitMQQueue('my_queue', ['declare' => true, 'durable' => true]);
        $msg = new RabbitMQMessage('test');
        $msg->setExchange($exchange);

        $rabbitMQ->publisher()->publish(
            $msg,
            'key'
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

        try {
            $consumer->consume(
                $messageConsumer,
                'key',
                null,
                new ConsumeConfig(
                    [
                        'wait_timeout' => 1,
                        'wait_timeout' => 1,
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
        } catch (AMQPTimeoutException $e) {
            //
        }
    }

    public function testCanConsumeMessagesWithConfig()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $consumer = $rabbitMQ->consumer();

        $this->assertInstanceOf(RabbitMQConsumer::class, $consumer);

        $exchange = new RabbitMQExchange('unit_test', ['declare' => true, 'durable' => true]);
        $queue = new RabbitMQQueue('my_queue', ['declare' => true, 'durable' => true]);
        $msg = new RabbitMQMessage('test');
        $msg->setExchange($exchange);

        $rabbitMQ->publisher()->publish(
            $msg,
            'key'
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

        try {
            $consumer->consume(
                $messageConsumer,
                'key',
                null,
                new ConsumeConfig(
                    [
                        'wait_timeout' => 1,
                        'queue' => [
                            'name' => 'my_queue',
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
        } catch (AMQPTimeoutException $e) {
            //
        }
    }

    public function testCanConsumeMessagesFromFanout()
    {
        $rabbitMQ = new RabbitMQManager($this->app);
        $consumer = $rabbitMQ->consumer();

        $this->assertInstanceOf(RabbitMQConsumer::class, $consumer);

        $exchange = new RabbitMQExchange(
            'sample_exchange_2',
            [
                'durable' => true,
                'declare' => true,
                'type' => AMQPExchangeType::DIRECT,
            ]
        );
        // $queue = new RabbitMQQueue('my_queue', ['declare' => true, 'durable' => true]);
        $msg = new RabbitMQMessage('test');
        $msg->setExchange($exchange);

        $messageConsumer = new RabbitMQGenericMessageConsumer(
            function (RabbitMQIncomingMessage $message) {
                $this->assertEquals('test', $message->getStream());
                $message->getDelivery()->getConfig()->put('body', 'quit');
                $message->getDelivery()->acknowledge();
                return false;
            },
            $this,
        );

        $messageConsumer->setExchange($exchange);

        try {
            $consumer->consume(
                $messageConsumer,
                'key',
                null,
                new ConsumeConfig(['wait_timeout' => 1, 'qos' => ['enabled' => true]])
            );
        } catch (AMQPTimeoutException $e) {
            //
        }
    }
}
