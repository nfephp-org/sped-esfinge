<?php

namespace NFePHP\Esfinge;

use InvalidArgumentException;
use RuntimeException;
use NFePHP\Esfinge\Response;
use NFePHP\Esfinge\Base;

class Tools extends Base
{
    const TK_O = 'O';
    const TK_I = 'I';
    const TK_F = 'F';
    const TK_C = 'C';
    const TK_S = 'S';
    
    /**
     * Endereços principais dos webservices
     * @var array
     */
    protected $url = [
        '1' => 'https://esfingews.tce.sc.gov.br',
        '2' => 'https://desenv2.tce.sc.gov.br:7443',
    ];
    /**
     * Competência bimestral no formato: AAAABB, onde:
     * AAAA = ano a ser enviado os dados
     * BB = bimestre de 01 até 06
     * @var string
     */
    private $competencia;
    /**
     * Token de segurança e queue
     * hash com 36 caracteres aleatórios
     * @var string
     */
    private $tokenid;
    /**
     * Flag iniciar tranferencia
     * @var bool
     */
    private $flagIniciar = false;
    /**
     * Datahora da ultima solicitação da situação do token
     * @var timestramp
     */
    private $tsLastSitToken;
    

    public function __construct($configJson = '')
    {
        parent::__construct($configJson);
    }
    
    /**
     * Define o período de competência das informações
     * formado AAAABB sendo BB o bimestre de 01 até 06
     * @param string $valor
     */
    public function setCompetencia($aaaabb)
    {
        if (!is_numeric($aaaabb)) {
            throw new InvalidArgumentException('O periodo de competência é uma informação APENAS numérica.');
        }
        $bm = intval(substr($aaaabb, -2));
        if ($bm > 6 || $bm <= 0) {
            throw new InvalidArgumentException('O bimestre pode ser de 01 até 06 APENAS.');
        }
        $this->competencia = $aaaabb;
    }
    
    /**
     * Retorna o período de competência informado
     * @return string
     */
    public function getCompetencia()
    {
        return $this->competencia;
    }
    
    /**
     * Operações com token
     * O método pode ser:
     *   C - cancela a transferencia
     *   F - finaliza a transferencia
     *   I - inica a transferencia
     *   O - Obtem o token para realizar as operações
     *   S - Verifica a situação do token
     * @param string $method
     */
    public function token($method = 'O')
    {
        $uri = $this->url[$this->tpAmb].'/esfinge/services/tokenWS';
        $namespace = 'http://token.ws.tce.sc.gov.br/';
        
        switch ($method) {
            case 'C':
                if ($this->flagIniciar === false) {
                    //não está iniciada a tranferencia então não dá para cancelar
                    throw new RuntimeException('A tranferencia não foi iniciada, então não pode ser cancelada');
                }
                //cancela as operações realizadas com um determinado token
                //se OK o token é removido e todas as operações com ele
                //realizadas são descartadas
                $x = 'http://token.ws.tce.sc.gov.br/FilaAcesso/cancelarTransferencia';
                $met = 'cancelarTransferencia';
                $body = "<cancelarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</cancelarTransferencia>";
                $retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat']) {
                    $this->tokenid = '';
                    $this->flagIniciar = false;
                }
                break;
            case 'F':
                if ($this->flagIniciar === false) {
                    //não está iniciada a tranferencia então não dá para finalizar
                    throw new RuntimeException('A tranferencia não foi iniciada, então não pode ser finalizada');
                }
                //Ao final da transferência caso queria confirmar todos os elementos inseridos
                //(que não retornaram erro) nesta sessão, ou seja todos os elementos ligados a
                //determinado token passado para o serviço. Uma vez executado este serviço
                //o token atual será descartado.
                $met = 'finalizarTransferencia';
                $body = "<finalizarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</finalizarTransferencia>";
                $retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat']) {
                    $this->tokenid = '';
                    $this->flagIniciar = false;
                }
                break;
            case 'I':
                if ($this->tokenid == '') {
                    //não é possivel iniciar sem um token valido
                    throw new RuntimeException('Não é possivel iniciar a tranferência sem um token valido');
                }
                //Antes de iniciar a transferência dos dados propriamente dita será necessário executar
                //o serviço iniciarTransferencia
                $met = 'iniciarTransferencia';
                $body = "<iniciarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</iniciarTransferencia>";
                $retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat']) {
                    $this->flagIniciar = true;
                }
                break;
            case 'O':
                if ($this->tokenid != '') {
                    //já existe um token
                    throw new RuntimeException('Já existe um token aberto.');
                }
                $met = 'obterToken';
                $body = "<obterToken xmlns=\"$namespace\">"
                    . "<codigoUg>$this->codigoUnidadeGestora</codigoUg>"
                    . "</obterToken>";
                $retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat'] && $resp['chaveToken'] != '') {
                    $this->tokenid = $resp['chaveToken'];
                }
                break;
            case 'S':
                //Retorna a situação do token passado como parâmetro. Para evitar solicitações
                //indefinidas a este serviço o sistema punirá com a remoção do token da fila
                //sempre que for feita duas chamadas seguidas do serviço obterSituacaoToken
                //em menos de cinco segundos.
                if ($this->tokenid == '') {
                    //não é possivel verificar o token
                    throw new RuntimeException('Não existe um token aberto.');
                }
                //se tentativa de verificação ocorrer em menos de 2 seg
                //retorna como OK
                if ((time()-$this->tsLastSitToken) <= 2) {
                    $resp = [
                        'bStat' => true,
                        'message' => 'Situação token obtida com sucesso',
                        'status' => 'OK',
                        'posicao' => 1,
                        'situacao' => 'Pronto para envio ou consulta'
                    ];
                    break;
                }
                $met = 'obterSituacaoToken';
                $body = "<obterSituacaoToken xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</obterSituacaoToken>";
                $retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                $this->tsLastSitToken = time();
                break;
        }
        return $resp;
    }
    
    /**
     * Servidor
     * @param array $data
     * @param string $method
     * @return array
     */
    public function servidor($data = array(), $method = 'L')
    {
        $uri = 'servidorWS';
        $namespace = 'http://servidor.ws.tce.sc.gov.br/';
        $met = 'servidor'.$method;
        //obtêm o token para essa operação
        if ($this->tokenid == '') {
            $this->token('O');
        }
        if (! $this->flagIniciar) {
            //soliciar inicio de transferencia
            $this->token('I');
        }
        if ($this->tokenid != '' && $this->flagIniciar) {
            //constroi a mensagem
            $msg = $this->buildMsgH($method, $namespace);
            $msg .= $this->buildMsgB($method, $data);
            //envia a mensagem via cURL
            $retorno = $this->envia($msg, $body, $method);
            $resp =  Response::readReturn($met, $retorno);
            //se sucesso
            $this->token('F');
        }
    }
    
    /**
     * Situação Servidor Folha Pagamento
     * @param array $data
     * @param string $method
     * @return array
     */
    public function situacaoServidorFolhaPagamento($data = array(), $method = 'L')
    {
        $uri = 'situacaoServidorFolhaPagamentoWS';
        $namespace = 'http://situacaoservidorfolhapagamento.ws.tce.sc.gov.br/';
        $met = 'situacaoServidorFolhaPagamento'.$method;
        //constroi a mensagem
        $msg = $this->buildMsgH($method, $namespace);
        $msg .= $this->buildMsgB($method, $data);
        //envia a mensagem via cURL
        $retorno = $this->envia($msg, $body, $method);
        $resp =  Response::readReturn($met, $retorno);
    }

    /**
     * Componentes Folha Pagamento
     * @param array $data
     * @param string $method
     * @return array
     */
    public function componentesFolhaPagamento($data = array(), $method = 'L')
    {
        $uri = 'componentesFolhaPagamentoWS';
        $namespace = 'http://componentesfolhapagamento.ws.tce.sc.gov.br/';
        $met = 'componentesFolhaPagamento'.$method;
        //constroi a mensagem
        $msg = $this->buildMsgH($method, $namespace);
        $msg .= $this->buildMsgB($method, $data);
        //envia a mensagem via cURL
        $retorno = $this->envia($msg, $body, $method);
        $resp =  Response::readReturn($met, $retorno);
    }

    /**
     * Folha Pagamento
     * @param array $data
     * @param string $method
     * @return array
     */
    public function folhaPagamento($data = array(), $method = 'L')
    {
        if (empty($data)) {
            return;
        }
        $uri = 'folhaPagamentoWS';
        $namespace = 'http://folhapagamento.ws.tce.sc.gov.br/';
        $met = 'folhaPagamento'.$method;
        //constroi a mensagem
        $msg = $this->buildMsgH($method, $namespace);
        $msg .= $this->buildMsgB($method, $data);
        //envia a mensagem via cURL
        $retorno = $this->envia($msg, $body, $method);
        $resp =  Response::readReturn($met, $retorno);
    }
}
