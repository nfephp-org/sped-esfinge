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
    //listar dados componentes folha pagamento
    $filtros[] = [
        'campo' => '',
        'valor' => '',
        'tipo' => '',
        'tamanho' => '',
        'sufixo_operador' => ''
    ];
    $data = [
        'pagina' => '1',
        'filtros' => $filtros
    ];
    //este método faz o envio,
    //se ainda não tiver o TOKEN -> Obtem  (automático)
    //se ainda não tiver iniciado -> inicia (automático)
    $retorno = $tools->componentesFolhaPagamento($data, 'L');
    //finalizar
    //a finalização não é automática é deve ser realizada 
    //após todo o envio de dados 
    $resp = $tools->token($tools::TK_FINALIZA);
} catch (Exception $e) {
    echo "Houve uma exceção: " . $e->getMessage();
}    

