<?php

use Kunnu\RabbitMQ\RabbitMQManager;

return [
    // Default connection (a key from 'connections')
    'defaultConnection' => env('RABBITMQ_CONNECTION', 'rabbitmq'),

    // Connections
    'connections' => [
        'rabbitmq' => [
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'username' => env('RABBITMQ_USERNAME', ''),
            'password' => env('RABBITMQ_PASSWORD', ''),
            'vhost' => env('RABBITMQ_VHOST', '/'),
            'ssl_options' => [],
            'ssl_protocol' => 'ssl',
            'connect_options' => [],
        ]
    ],
    'channel_id' => null,

    'message' => [
        'content_type'     => 'text/plain',
        'delivery_mode'    => env('RABBITMQ_MESSAGE_DELIVERY_MODE', 2), // TODO
        'content_encoding' => 'UTF-8',
    ],

    'exchange' => [
        'name' => env('RABBITMQ_EXCHANGE_NAME', 'amq.topic'),
        'declare' => env('RABBITMQ_EXCHANGE_DECLARE', false),
        'type' => env('RABBITMQ_EXCHANGE_TYPE', 'topic'),
        'passive' => env('RABBITMQ_EXCHANGE_PASSIVE', false),
        'durable' => env('RABBITMQ_EXCHANGE_DURABLE', true),
        'auto_delete' => env('RABBITMQ_EXCHANGE_AUTO_DEL', false),
        'internal' => env('RABBITMQ_EXCHANGE_INTERNAL', false),
        'nowait' => env('RABBITMQ_EXCHANGE_NOWAIT', false),
        'properties' => [],
    ],

    'queue' => [
        'declare' => env('RABBITMQ_QUEUE_DECLARE', false),
        'passive' => env('RABBITMQ_QUEUE_PASSIVE', false),
        'durable' => env('RABBITMQ_QUEUE_DURABLE', true),
        'exclusive' => env('RABBITMQ_QUEUE_EXCLUSIVE', false),
        'auto_delete' => env('RABBITMQ_QUEUE_AUTO_DEL', false),
        'nowait' => env('RABBITMQ_QUEUE_NOWAIT', false),
        'd_properties' => [], // queue_declare properties/arguments
        'b_properties' => [], // queue_bind properties/arguments
    ],

    'consumer' => [
        'tag' => env('RABBITMQ_CONSUMER_TAG', ''),
        'no_local' => env('RABBITMQ_CONSUMER_NO_LOCAL', false),
        'no_ack' => env('RABBITMQ_CONSUMER_NO_ACK', false),
        'exclusive' => env('RABBITMQ_CONSUMER_EXCLUSIVE', false),
        'nowait' => env('RABBITMQ_CONSUMER_NOWAIT', false),
        'ticket' => null,
        'properties' => [],
    ],

    'qos' => [
        'enabled' => env('RABBITMQ_QOS_ENABLED', false),
        'qos_prefetch_size' => env('RABBITMQ_QOS_PREF_SIZE', 0),
        'qos_prefetch_count' => env('RABBITMQ_QOS_PREF_COUNT', 1),
        'qos_a_global' => env('RABBITMQ_QOS_GLOBAL', false),
    ],
];
