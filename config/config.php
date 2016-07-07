<?php

$config = [
    'username' => '',
    'password' => '',
    'codigoUnidadeGestora' => '',
    'aProxyConf' => [
        'proxyIp' => '',
        'proxyPort' => '',
        'proxyUser' => '',
        'proxyPass' => ''
    ]
];

file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT));