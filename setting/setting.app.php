<?php
$collection = new \Gap\Config\ConfigCollection();
$collection
    ->set("app", [
        "Tec\User" => [
            "dir" => "app/tec/user",
        ],
    ]);
return $collection;
