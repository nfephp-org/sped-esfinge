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
    //Enviar dados Componentes Folha de Pagamento
    $data[] = [
        'idRetorno' => '',
        'mensagemProcessamento' => '',
        'codigoProcessamento' => '',
        'codigoComponente' => '',
        'anoMesComponente' => '',
        'especificacaoNaturezaComponente' => '',
        'descricaoComponente' => '',
        'baseLegalComponente' => '',
        'indicadorComponente' => ''
    ];
    $data[] = [
        'idRetorno' => '',
        'mensagemProcessamento' => '',
        'codigoProcessamento' => '',
        'codigoComponente' => '',
        'anoMesComponente' => '',
        'especificacaoNaturezaComponente' => '',
        'descricaoComponente' => '',
        'baseLegalComponente' => '',
        'indicadorComponente' => ''
    ];
    //este método faz o envio,
    //se ainda não tiver o TOKEN -> Obtem  (automático)
    //se ainda não tiver iniciado -> inicia (automático)
    $retorno = $tools->componentesFolhaPagamento($data, 'E');
    //finalizar
    //a finalização não é automática é deve ser realizada 
    //após todo o envio de dados 
    $resp = $tools->token($tools::TK_FINALIZA);
} catch (Exception $e) {
    echo "Houve uma exceção: " . $e->getMessage();
}    
