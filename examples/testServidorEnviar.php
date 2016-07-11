<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\Esfinge\Tools;

try {
    //instanciar a classe passando a configuração básica
    $tools = new Tools('../config/config.json');
    //definir o periodo de competência
    $tools->setCompetencia('201602');
    //Enviar dados servidor
    //NOTA: deixando o campo em branco '' ele não será incluso no XML
    //NOTA: não passando o campo ele também não será incluso no XML
    $data[] = [
        'idRetorno' => '1',
        'mensagemProcessamento' => 'sei la',
        'codigoProcessamento' => '123',
        'numeroMatricula' => '00001',
        'nomeServidor' => 'Ciclano de Tal',
        'dataNascimento' => '12-11-1965',
        'nomeMae' => 'Fulana de Tal',
        'nomePai' => 'Beltrano de Tal',
        'numeroCPF' => '12345678901234',
        'numeroRegistroGeral' => '11111111111',
        'numeroTituloEleitoral' => '2222222222',
        'numeroCertificadoReservista' => '3333333333',
        'numeroPisPasep' => '4444444444444',
        'codigoSexo' => 'M'
    ];
    $data[] = [
        'idRetorno' => '2',
        'mensagemProcessamento' => 'sei la',
        'codigoProcessamento' => '124',
        'numeroMatricula' => '00002',
        'nomeServidor' => 'Joana de Tal',
        'dataNascimento' => '01-01-1967',
        'nomeMae' => 'Fulana de Tal',
        'nomePai' => 'Beltrano de Tal',
        'numeroCPF' => '43210987654321',
        'numeroRegistroGeral' => '21111111111',
        'numeroTituloEleitoral' => '3222222222',
        'numeroCertificadoReservista' => '4333333333',
        'numeroPisPasep' => '5444444444444',
        'codigoSexo' => 'F'
    ];
    //este método faz o envio,
    //se ainda não tiver o TOKEN -> Obtem  (automático)
    //se ainda não tiver iniciado -> inicia (automático)
    $retorno = $tools->servidor($data, 'E');
    //finalizar
    //a finalização não é automática é deve ser realizada 
    //após todo o envio de dados 
    $resp = $tools->token($tools::TK_Finaliza);
} catch (Exception $e) {
    echo "Houve uma exceção: " . $e->getMessage();
}    

