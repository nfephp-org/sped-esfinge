<?php

namespace NFePHP\Esfinge;

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
     *
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
    
    public function setAmbiente($tpAmb)
    {
        if ($tpAmb == 1) {
            $this->tpAmb = 1;
        } else {
            $this->tpAmb = 2;
            $this->password = '123456';
        }
        
    }
    public function getToken($method = 'O')
    {
        $uri = 'tokenWS';
        $namespace = 'http://token.ws.tce.sc.gov.br/';
        
        switch ($method) {
            case 'C':
                //cancelarTransferencia
                $msg = '<cancelarTransferencia xmlns="'.$namespace.'">'
                    . '<chaveToken>'.$this->token.'</chaveToken>'
                    . '</cancelarTransferencia>';
                break;
            case 'F':
                //finalizarTransferencia
                $msg = '<tok:finalizarTransferencia xmlns="'.$namespace.'">'
                    . '<chaveToken>'.$this->token.'</chaveToken>'
                    . '</finalizarTransferencia>';
                break;
            case 'I':
                //iniciarTransferencia
                $msg = ' <tok:iniciarTransferencia xmlns="'.$namespace.'">'
                    . '<chaveToken>'.$this->token.'</chaveToken>'
                    . '</iniciarTransferencia>';
                break;
            case 'O':
                //obterToken
                $msg = '<obterToken xmlns="'.$namespace.'">'
                    . '<codigoUg>'.$this->codigoUnidadeGestora.'</codigoUg>
                    . </obterToken>';
                break;
            case 'S':
                //obterSituacaoToken
                $msg = '<obterSituacaoToken xmlns="'.$namespace.'">'
                    . '<chaveToken>'.$this->token.'</chaveToken>'
                    . '</obterSituacaoToken>';
                break;
        }
    }
    
    public function servidor($method = 'L')
    {
        $uri = 'servidorWS';
        $namespace = '';
        
    }
    
    
    public function situacaoServidorFolhaPagamento($method = 'L')
    {
        $uri = 'situacaoServidorFolhaPagamentoWS';
        $namespace = '';
    }

    
    public function componentesFolhaPagamento($method = 'L')
    {
        $uri = 'componentesFolhaPagamentoWS';
        $namespace = '';
    }

    
    public function folhaPagamento($method = 'L')
    {
        $uri = 'folhaPagamentoWS';
        $namespace = '';
    }
    
    protected function buildHeader()
    {
        $this->header = "<soap:Header>"
            . "<wsse:Security xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\" soap:mustUnderstand=\"1\">"
            . "<wsse:UsernameToken xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\" wsu:Id=\"G1d58378c-e8cd-4bbd-be22-fcc5c4313558\">"
            . "<wsse:Username>"
            . $this->user
            . "</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">"
            . $this->pass
            . "</wsse:Password>"
            . "</wsse:UsernameToken>"
            . "</wsse:Security>"
            . "</soap:Header>";
    }
}
