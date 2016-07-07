<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\Esfinge\Tools;

$tools = new Tools('../config/config.json');

$retorno = $tools->token($tools::TK_O);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

$retorno = $tools->token($tools::TK_I);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

$retorno = $tools->token($tools::TK_F);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

$retorno = $tools->token($tools::TK_C);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

$retorno = $tools->token($tools::TK_S);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';




