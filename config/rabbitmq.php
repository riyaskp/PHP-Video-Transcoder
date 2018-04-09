<?php
return [
    'class' => \mikemadisonweb\rabbitmq\Configuration::class,
    'auto_declare' => true,
    'connections' => [
        [
            'host' => '192.168.*.***',
            'port' => '5672',
            'user' => 'admin',
            'password' => '*******',
            'vhost' => '/',
            'heartbeat' => 0,
        ],
    ],
    'exchanges' => [
        [
            'name' => 'ConvertVideo',
            'type' => 'direct'
            // Refer to Defaults section for all possible options
        ]
    ],
    'queues' => [
        [
            'name' => 'convert_video',
            // Queue can be configured here the way you want it:
            'durable' => true,
            //'auto_delete' => false,
        ]
    ],
    'bindings' => [
        [
            'queue' => 'convert_video',
            'exchange' => 'ConvertVideo',
            'routing_keys' => ['convertVideoMp4'],
        ]
    ],
    'producers' => [
        [
            'name' => 'convertVideo',
        ]
    ],
    'consumers' => [
        [
            'name' => 'convertVideo',
            // Every consumer should define one or more callbacks for corresponding queues
            'callbacks' => [
                'convert_video' => \app\components\rabbitmq\ConvertVideo::class,
            ],
        ],
        [
            'name' => 'convertVideo1',
            // Every consumer should define one or more callbacks for corresponding queues
            'callbacks' => [
                'convert_video' => \app\components\rabbitmq\ConvertVideo1::class,
            ],
        ]
    ],
    'logger' => [
        'log' => false,
        'category' => 'application',
        'print_console' => true,
        'system_memory' => true,
    ],
];
