<?php

namespace NFePHP\Esfinge;

use InvalidArgumentException;
use RuntimeException;
use NFePHP\Esfinge\Response;
use NFePHP\Esfinge\Base;

class Tools extends Base
{
    const TK_OBTEM = 'O';
    const TK_INICIA = 'I';
    const TK_FINALIZA = 'F';
    const TK_CANCELA = 'C';
    const TK_STATUS = 'S';
    
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
    protected $competencia;
    /**
     * Token de segurança e queue
     * hash com 36 caracteres aleatórios
     * @var string
     */
    protected $tokenid;
    /**
     * Flag iniciar tranferencia
     * @var bool
     */
    protected $flagIniciar = false;
    /**
     * Datahora da ultima solicitação da situação do token
     * @var timestramp
     */
    protected $tsLastSitToken;
    
    /**
     * Construtor
     * @param string $configJson
     */
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
    public function token($method = self::TK_OBTEM)
    {
        $uri = $this->url[$this->tpAmb].'/esfinge/services/tokenWS';
        $namespace = 'http://token.ws.tce.sc.gov.br/';
        
        switch ($method) {
            case self::TK_CANCELA:
                //cancela as operações realizadas com um determinado token
                //se OK o token é removido e todas as operações com ele
                //realizadas são descartadas
                if ($this->flagIniciar === false) {
                    //não está iniciada a tranferencia então não dá para cancelar
                    throw new RuntimeException('A tranferencia não foi iniciada, então não pode ser cancelada');
                }
                $met = 'cancelarTransferencia';
                $body = "<svc:cancelarTransferencia>"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</svc:cancelarTransferencia>";
                $resp = $this->envia($uri, $namespace, $body, '', $met);
                if ($resp['bStat'] && $resp['status'] == 'OK') {
                    //cancelamento aceito
                    $this->tokenid = '';
                    $this->flagIniciar = false;
                }
                break;
            case self::TK_FINALIZA:
                //Ao final da transferência caso queria confirmar todos os elementos inseridos
                //(que não retornaram erro) nesta sessão, ou seja todos os elementos ligados a
                //determinado token passado para o serviço. Uma vez executado este serviço
                //o token atual será descartado.
                if ($this->flagIniciar === false) {
                    //não está iniciada a tranferencia então não dá para finalizar
                    throw new RuntimeException('A tranferencia não foi iniciada, então não pode ser finalizada');
                }
                $met = 'finalizarTransferencia';
                $body = "<svc:finalizarTransferencia>"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</svc:finalizarTransferencia>";
                $resp = $this->envia($uri, $namespace, $body, '', $met);
                if ($resp['bStat'] && $resp['status'] == 'OK') {
                    //finalização aceita
                    $this->tokenid = '';
                    $this->flagIniciar = false;
                }
                break;
            case self::TK_INICIA:
                //Antes de iniciar a transferência dos dados propriamente dita, será necessário executar
                //o serviço iniciarTransferencia
                if ($this->tokenid == '') {
                    //não é possivel iniciar sem um token valido
                    throw new RuntimeException('Não é possivel iniciar a tranferência sem um token valido');
                    //$this->token(self::TK_O);
                }
                if ($this->flagIniciar === true) {
                    $resp = [
                        'bStat' => true,
                        'message' => 'Início de transferência liberado',
                        'status' => 'OK'
                    ];
                    break;
                }
                $met = 'iniciarTransferencia';
                $body = "<svc:iniciarTransferencia>"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</svc:iniciarTransferencia>";
                $resp = $this->envia($uri, $namespace, $body, '', $met);
                if ($resp['bStat'] && $resp['status'] == 'OK') {
                    $this->flagIniciar = true;
                }
                break;
            case self::TK_OBTEM:
                //Retorna um token para a unidade gestora poder usar o serviço do TCE.
                //Permite somente um token por unidade gestora.
                if ($this->tokenid != '') {
                    $resp = [
                        'bStat' => true,
                        'message' => 'Token criado com sucesso',
                        'status' => 'OK',
                        'chaveToken' => $this->tokenid,
                        'posicao' => 2,
                        'situacao' => 'Pronto para envio ou consulta'
                    ];
                    break;
                }
                $met = 'obterToken';
                $body = "<svc:obterToken>"
                    . "<codigoUg>$this->codigoUnidadeGestora</codigoUg>"
                    . "</svc:obterToken>";
                $resp = $this->envia($uri, $namespace, $body, '', $met);
                if ($resp['bStat']
                    && $resp['chaveToken'] != ''
                    && $resp['status'] == 'OK'
                ) {
                    $this->tokenid = $resp['chaveToken'];
                }
                break;
            case self::TK_STATUS:
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
                $body = "<svc:obterSituacaoToken>"
                    . "<chaveToken>$this->tokenid</chaveToken>"
                    . "</svc:obterSituacaoToken>";
                $resp = $this->envia($uri, $namespace, $body, '', $met);
                $this->tsLastSitToken = time();
                break;
        }
        return $resp;
    }
    
    /**
     * Inicia o processo de tranferência de dados
     * @param array $data
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function obterTokenIniciarTransferencia($data = array())
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Não foram passados dados para o método');
        }
        $this->token(self::TK_OBTEM);
        $this->token(self::TK_INICIA);
        if ($this->tokenid == '' || $this->flagIniciar === false) {
            throw new RuntimeException("Falha token:$this->tokenid , Iniciar: $this->flagIniciar");
        }
    }

    /**
     * Servidor
     *  se ainda não tiver o TOKEN -> Obtem  (automático)
     *  se ainda não tiver iniciado -> inicia (automático)
     * @param array $data
     * @param string $method
     * @return array
     */
    public function servidor($data = array(), $method = 'L')
    {
        $this->obterTokenIniciarTransferencia($data);
        $uri = $this->url[$this->tpAmb].'/esfinge/services/servidorWS';
        $namespace = 'http://servidor.ws.tce.sc.gov.br/';
        $met = 'servidor'.$method;
        //envia a mensagem via cURL
        $resp = $this->envia($uri, $namespace, $data, $method, $met);
        return $resp;
    }
    
    /**
     * Situação Servidor Folha Pagamento
     *  se ainda não tiver o TOKEN -> Obtem  (automático)
     *  se ainda não tiver iniciado -> inicia (automático)
     * @param array $data
     * @param string $method
     * @return array
     */
    public function situacaoServidorFolhaPagamento($data = array(), $method = 'L')
    {
        $this->obterTokenIniciarTransferencia($data);
        $uri = $this->url[$this->tpAmb].'/esfinge/services/situacaoServidorFolhaPagamentoWS';
        $namespace = 'http://situacaoservidorfolhapagamento.ws.tce.sc.gov.br/';
        $met = 'situacaoServidorFolhaPagamento'.$method;
        $resp = $this->envia($uri, $namespace, $data, $method, $met);
        return $resp;
    }

    /**
     * Componentes Folha Pagamento
     *  se ainda não tiver o TOKEN -> Obtem  (automático)
     *  se ainda não tiver iniciado -> inicia (automático)
     * @param array $data
     * @param string $method
     * @return array
     */
    public function componentesFolhaPagamento($data = array(), $method = 'L')
    {
        $this->obterTokenIniciarTransferencia($data);
        $uri = $this->url[$this->tpAmb].'/esfinge/services/componentesFolhaPagamentoWS';
        $namespace = 'http://componentesfolhapagamento.ws.tce.sc.gov.br/';
        $met = 'componentesFolhaPagamento'.$method;
        $resp = $this->envia($uri, $namespace, $data, $method, $met);
        return $resp;
    }

    /**
     * Folha Pagamento
     *  se ainda não tiver o TOKEN -> Obtem  (automático)
     *  se ainda não tiver iniciado -> inicia (automático)
     * @param array $data
     * @param string $method
     * @return array
     */
    public function folhaPagamento($data = array(), $method = 'L')
    {
        $this->obterTokenIniciarTransferencia($data);
        $uri = $this->url[$this->tpAmb].'/esfinge/services/folhaPagamentoWS';
        $namespace = 'http://folhapagamento.ws.tce.sc.gov.br/';
        $met = 'folhaPagamento'.$method;
        $resp = $this->envia($uri, $namespace, $data, $method, $met);
        return $resp;
    }
}
