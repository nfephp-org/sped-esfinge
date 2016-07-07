<?php

$config = [
    'username' => '',
    'password' => '',
    'codigoUnidadeGestora' => ''
];

file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT));