<?php
$collection = new \Gap\Config\ConfigCollection();

$collection
    ->set('site', [
        'default' => [
            'host' => 'user.%baseHost%',
        ],
        'api' => [
            'host' => 'user-api%baseHost%',
        ],
        'static' => [
            'host' => 'user-static.%baseHost%',
            'dir' => '%baseDir%/site/static',
        ],
    ]);

return $collection;
