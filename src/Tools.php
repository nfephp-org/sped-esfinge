<?php

namespace NFePHP\Esfinge;

use InvalidArgumentException;

class Tools
{
    /**
     * Endereços principais dos webservices
     * @var array
     */
    protected $url = [
        '1' => 'https://esfingews.tce.sc.gov.br',
        '2' => 'https://desenv2.tce.sc.gov.br:7443',
    ];
    /**
     * Tipo de Ambiente 
     * 1 - produção
     * 2 - HOmologação
     * @var int
     */
    protected $tpAmb = 2;
    /**
     * Header da mensagem SOAP
     * @var string
     */
    protected $header;
    /**
     * Nome do usuário do sistema 
     * @var string
     */
    private $username;
    /**
     * Password do usuário do sistema
     * @var string
     */
    private $password;
    /**
     * Código da Unidade Gestora conforme informado pelo serviço listar
     * da tabela unidades gestoras
     * @var string
     */
    private $codigoUnidadeGestora;
    /**
     * Competência bimestral no formato: AAAABB, onde:
     * AAAA = ano a ser enviado os dados
     * BB = bimestre de 01 até 06
     * @var string
     */
    private $competencia;
    /**
     * Token de seguraça e queue 
     * hash com 36 caracteres aleatórios
     * @var string
     */
    private $token;
    

    public function __construct($configJson = '')
    {
        if (empty($configJson)) {
            return;
        }
        $config = $configJson;
        if (is_file($configJson)) {
            $config = file_get_contents($configJson);
        } 
        $config = json_decode($config);
        
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->codigoUnidadeGestora = $config['codigoUnidadeGestora'];
        
        $this->setAmbiente($config['tpAmb']);
    }
    
    /**
     * Seta o ambiente de trabalho
     * 1 - Produção
     * 2 - Homologação 
     * @param int $tpAmb
     */
    public function setAmbiente($tpAmb)
    {
        if ($tpAmb == 1) {
            $this->tpAmb = 1;
        } else {
            $this->tpAmb = 2;
            //sobrescreve a senha que é diferente no ambiente de teste
            $this->password = '123456';
        }
    }
    
    /**
     * Define o período de competência das informações
     * formado AAAABB sendo BB o bimestre de 01 até 06
     * @param string $valor
     */
    public function setCompetencia($aaaabb)
    {
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
        $uri = 'tokenWS';
        $namespace = 'http://token.ws.tce.sc.gov.br/';
        
        switch ($method) {
            case 'C':
                //cancelarTransferencia
                $msg = "<cancelarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->token</chaveToken>"
                    . "</cancelarTransferencia>";
                break;
            case 'F':
                //finalizarTransferencia
                $msg = "<finalizarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->token</chaveToken>"
                    . "</finalizarTransferencia>";
                break;
            case 'I':
                //iniciarTransferencia
                $msg = "<iniciarTransferencia xmlns=\"$namespace\">"
                    . "<chaveToken>$this->token</chaveToken>"
                    . "</iniciarTransferencia>";
                break;
            case 'O':
                //obterToken
                $msg = "<obterToken xmlns=\"$namespace\">"
                    . "<codigoUg>$this->codigoUnidadeGestora</codigoUg>"
                    . "</obterToken>";
                break;
            case 'S':
                //obterSituacaoToken
                $msg = "<obterSituacaoToken xmlns=\"$namespace\">"
                    . "<chaveToken>$this->token</chaveToken>"
                    . "</obterSituacaoToken>";
                break;
        }
    }
    
    
    public function servidor($data = array(), $method = 'L')
    {
        $uri = 'servidorWS';
        $namespace = 'http://servidor.ws.tce.sc.gov.br/';
        
    }
    
    
    public function situacaoServidorFolhaPagamento($data = array(), $method = 'L')
    {
        $uri = 'situacaoServidorFolhaPagamentoWS';
        $namespace = 'http://situacaoservidorfolhapagamento.ws.tce.sc.gov.br/';
        
    }

    
    public function componentesFolhaPagamento($data = array(), $method = 'L')
    {
        $uri = 'componentesFolhaPagamentoWS';
        $namespace = 'http://componentesfolhapagamento.ws.tce.sc.gov.br/';
        
    }

    
    public function folhaPagamento($data = array(), $method = 'L')
    {
        if (empty($data)) {
            return;
        }
        
        $uri = 'folhaPagamentoWS';
        $namespace = 'http://folhapagamento.ws.tce.sc.gov.br/';
        
        //constroi a mensagem
        $msg = $this->buildMsgH($method, $namespace);
        $msg .= $this->buildMsgB($method, $data);
        //envia a mensagem via cURL
        
        
    }
    
    /**
     * Monta as tags com base na chave e no valor do array
     * @param array $data
     * @return string
     */
    protected function addTag($data)
    {
        $ret = '';
        foreach($data as $key => $value) {
            $ret .= "<$key>$value</$key>";
        }
        return $ret;
    }
    
    /**
     * Monta o conjunto de Body na função enviar
     * @param string $key
     * @param array $data
     * @return string
     */
    protected function buildEnviarB($key, $data)
    {
        if (count($data) > 5000) {
            return;
        }
        $msg = '';
        foreach ($data as $field) {    
            $msg .= "<$key>";
            $msg .= $this->addTag($field);    
            $msg .= "</$key>";
        }            
        $msg .= '</enviar>';
        return $msg;
    }
    
    /**
     * Monta o conjunto Body da função Listar
     * @param string $pagina
     * @param array $filtros
     * @return string
     */
    protected function buildListarB($pagina = '', $filtros = [])
    {
        $msg = '<PAGINA>'.$pagina.'</PAGINA>';
        foreach ($filtros as $filtro) {
            $f = '<filtros>';
            $f .= $this->addTag($filtro);
            $f .= '</filtros>';
            $msg .= $f;
        };
        $msg .= '</listar>';        
        return $msg;
    }
    
    /**
     * Monta a primeira parte de todas mensagens
     * @param string $namespace
     * @return string
     */
    protected function buildMsgH($tipo, $namespace)
    {
        $key = 'enviar';
        $codug = '';
        if ($tipo == 'L') {
            $key = 'listar';
            $codug = "<codigoUg>$this->codigoUnidadeGestora</codigoUg>";
        }
        $msg = "<$key xmlns=\"$namespace\">";
        $msg .= $codug; 
        $msg .= "<chaveToken>$this->token</chaveToken>";
        $msg .= "<competencia>$this->competencia</competencia>";
        return $msg;
    }
    
    /**
     * Monta o corpo de todas as mensagens
     * @param string $tipo
     * @param array $data
     * @return string
     */
    protected function buildMsgB($tipo, $data)
    {
        if ($tipo == 'L') {
            $msg = $this->buildListarB($pagina, $filtros);
        } elseif ($tipo == 'E') {
            $msg = $this->buildEnviarB($key, $data);
        }
        return $msg;
    }

    /**
     * Constroi o header da mensagem SOAP
     */
    protected function buildHeader()
    {
        $this->header = "<soap:Header>"
            . "<wsse:Security xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\" soap:mustUnderstand=\"1\">"
            . "<wsse:UsernameToken xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\">"
            . "<wsse:Username>"
            . $this->username
            . "</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">"
            . $this->password
            . "</wsse:Password>"
            . "</wsse:UsernameToken>"
            . "</wsse:Security>"
            . "</soap:Header>";
    }
}
