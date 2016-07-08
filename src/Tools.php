<?php

namespace NFePHP\Esfinge;

use InvalidArgumentException;
use NFePHP\Esfinge\Response;
use NFePHP\Esfinge\Base;
use NFePHP\Esfinge\Soap\CurlSoap;

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
                //cancela as operações realizadas com um determinado token
                //se OK o token é removido e todas as operações com ele
                //realizadas são descartadas
                $x = 'http://token.ws.tce.sc.gov.br/FilaAcesso/cancelarTransferencia';
                $met = 'cancelarTransferencia';
                $body = "<cancelarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</cancelarTransferencia>";
                $retorno = file_get_contents('../tests/fixtures/responseCancelarTransferencia.xml');
                //$retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat']) {
                    $this->tokenid = '';
                    $this->flagIniciar = false;
                }
                break;
            case 'F':
                $met = 'finalizarTransferencia';
                $body = "<finalizarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</finalizarTransferencia>";
                $retorno = file_get_contents('../tests/fixtures/responseFinalizarTransferencia.xml');
                //$retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat']) {
                    $this->tokenid = '';
                    $this->flagIniciar = false;
                }
                break;
            case 'I':
                $met = 'iniciarTransferencia';
                $body = "<iniciarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</iniciarTransferencia>";
                $retorno = file_get_contents('../tests/fixtures/responseIniciarTransferencia.xml');
                //$retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat']) {
                    $this->flagIniciar = true;
                }
                break;
            case 'O':
                $met = 'obterToken';
                $body = "<obterToken xmlns=\"$namespace\">"
                    . "<codigoUg>$this->codigoUnidadeGestora</codigoUg>"
                    . "</obterToken>";
                $retorno = file_get_contents('../tests/fixtures/responseObterToken.xml');
                //$retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
                if ($resp['bStat'] && $resp['chaveToken'] != '') {
                    $this->tokenid = $resp['chaveToken'];
                }
                break;
            case 'S':
                $met = 'obterSituacaoToken';
                $body = "<obterSituacaoToken xmlns=\"$namespace\">"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</obterSituacaoToken>";
                $retorno = file_get_contents('../tests/fixtures/responseObterSituacaoToken.xml');
                //$retorno = $this->oSoap->send($uri, $namespace, $this->header, $body, $met);
                $resp =  Response::readReturn($met, $retorno);
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
