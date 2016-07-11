<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\Esfinge\Tools;

$tools = new Tools('../config/config.json');

//obter
$retorno = $tools->token($tools::TK_OBTEM);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//iniciar
$retorno = $tools->token($tools::TK_INICIA);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//cancelar
$retorno = $tools->token($tools::TK_CANCELA);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
die;



//finalizar
$retorno = $tools->token($tools::TK_FINALIZA);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//obter e iniciar para poder cancelar
$retorno = $tools->token($tools::TK_OBTEM);
$retorno = $tools->token($tools::TK_INICIA);
//cancelar
$retorno = $tools->token($tools::TK_CANCELA);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
die;
//obter para poder verificar
$retorno = $tools->token($tools::TK_OBTEM);
//verificar situação do token
$retorno = $tools->token($tools::TK_STATUS);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
//verificar situação do token
$retorno = $tools->token($tools::TK_STATUS);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';




