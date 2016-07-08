<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\Esfinge\Tools;

$tools = new Tools('../config/config.json');

//obter
$retorno = $tools->token($tools::TK_O);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//iniciar
$retorno = $tools->token($tools::TK_I);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//finalizar
$retorno = $tools->token($tools::TK_F);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//obter e iniciar para poder cancelar
$retorno = $tools->token($tools::TK_O);
$retorno = $tools->token($tools::TK_I);
//cancelar
$retorno = $tools->token($tools::TK_C);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//obter para poder verificar
$retorno = $tools->token($tools::TK_O);
//verificar situação do token
$retorno = $tools->token($tools::TK_S);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
//verificar situação do token
$retorno = $tools->token($tools::TK_S);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';




