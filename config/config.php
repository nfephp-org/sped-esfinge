<?php

$config = [
    'atualizacao' => date('Y-m-d H:i:s'),
    'tpAmb' => 2,
    'username' => '1006',
    'password' => '123456',
    'codigoUnidadeGestora' => '1006',
    'pathFiles' => '/var/esfinge',
    'aProxyConf' => [
        'proxyIp' => '',
        'proxyPort' => '',
        'proxyUser' => '',
        'proxyPass' => ''
    ]
];

file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT));