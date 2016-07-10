<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\Esfinge\Tools;

$tools = new Tools('../config/config.json');
/*
//obter
$retorno = $tools->token($tools::TK_Obtem);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//iniciar
$retorno = $tools->token($tools::TK_Inicia);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';

//finalizar
$retorno = $tools->token($tools::TK_Finaliza);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
*/
//obter e iniciar para poder cancelar
$retorno = $tools->token($tools::TK_Obtem);
 
 
$retorno = $tools->token($tools::TK_Inicia);


//cancelar
$retorno = $tools->token($tools::TK_Cancela);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
die;
//obter para poder verificar
$retorno = $tools->token($tools::TK_Obtem);
//verificar situação do token
$retorno = $tools->token($tools::TK_Status);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';
//verificar situação do token
$retorno = $tools->token($tools::TK_Status);
echo '<pre>';
print_r($retorno);
echo '</pre>';
echo '<BR>';




