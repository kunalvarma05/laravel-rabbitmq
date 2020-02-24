<?php

namespace Kunnu\RabbitMQ\Test;

use Kunnu\RabbitMQ\RabbitMQ;
use Illuminate\Foundation\Application;
use Kunnu\RabbitMQ\RabbitMQServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('rabbitmq', include(__DIR__ . '/../src/config/rabbitmq.php'));
    }

    protected function getPackageProviders($app)
    {
        return [RabbitMQServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'RabbitMQ' => RabbitMQ::class,
        ];
    }
}
