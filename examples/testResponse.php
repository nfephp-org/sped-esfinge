<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\Esfinge\Response;

$xml = file_get_contents('../tests/fixtures/resp.xml');
$ret = Response::readReturn('listarArquivo', $xml);
echo '<pre>';
print_r($ret);
echo '</pre>';
