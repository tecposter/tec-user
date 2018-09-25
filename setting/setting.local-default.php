<?php
$collection = new \Gap\Config\ConfigCollection();

$collection
    ->set('debug', true)
    ->set('baseDir', realpath(__DIR__ . '/../'))
    ->set('baseHost', 'tecposter.cn')
    ->set('front', [
        'port' => 8787
    ])
    ->set('local', [
        'db' => [
            'host' => 'db',
            'database' => 'tec',
            'username' => 'tec',
            'password' => '123456789'
        ],
        'cache' => [
            'host' => 'redis'
        ],
        'session' => [
            'save_handler' => 'redis',
            'save_path' => 'tcp://redis:6379?database=10',
            'subdomain' => 'user'
        ]
    ]);

return $collection;
