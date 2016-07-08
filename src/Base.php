<?php

namespace NFePHP\Esfinge;

use InvalidArgumentException;
use NFePHP\Esfinge\Soap\CurlSoap;
use NFePHP\Esfinge\Files\FileFolders;

class Base
{
    
    protected $errors;
    /**
     * tpAmb
     * @var int
     */
    protected $tpAmb = 2;
    /**
     * ambiente
     * @var string
     */
    protected $ambiente = 'homologacao';
    /**
     * Diretorio para gravar arquivos de LOG
     * @var string
     */
    protected $pathFiles = '';
    /**
     * aConfig
     * @var array
     */
    protected $aConfig = array();
    /**
     * aProxy
     * @var array
     */
    protected $aProxy = array();
    /**
     * soapTimeout
     * @var int
     */
    protected $soapTimeout = 10;
    /**
     * oSoap
     * @var Object Class
     */
    protected $oSoap;
    /**
     * soapDebug
     * @var string
     */
    protected $soapDebug = '';
        /**
     * Header da mensagem SOAP
     * @var string
     */
    protected $header;
    /**
     * Nome do usuário do sistema
     * @var string
     */
    protected $username;
    /**
     * Password do usuário do sistema
     * @var string
     */
    protected $password;
    /**
     * Código da Unidade Gestora conforme informado pelo serviço listar
     * da tabela unidades gestoras
     * @var string
     */
    protected $codigoUnidadeGestora;
    
    /**
     * Contrutor
     * @param string $configJson
     */
    public function __construct($configJson = '')
    {
        if (empty($configJson)) {
            throw new InvalidArgumentException('A configuração deve ser passada.');
        }
        $config = $configJson;
        if (is_file($configJson)) {
            $config = file_get_contents($configJson);
        }
        $this->aConfig = json_decode($config, true);
        
        $this->username = $this->aConfig['username'];
        $this->password = $this->aConfig['password'];
        $this->codigoUnidadeGestora = $this->aConfig['codigoUnidadeGestora'];
        $this->aProxy = $this->aConfig['aProxyConf'];
        $this->setAmbiente($this->aConfig['tpAmb']);
        $this->pathFiles = $this->aConfig['pathFiles'];
        $this->loadSoapClass();
    }
    
    /**
     * Seta o ambiente de trabalho
     * 1 - Produção
     * 2 - Homologação
     * @param int $tpAmb
     */
    public function setAmbiente($tpAmb = 2)
    {
        if ($tpAmb == 1) {
            $this->tpAmb = 1;
            $this->ambiente = 'producao';
        } else {
            $this->tpAmb = 2;
            $this->ambiente = 'homologacao';
            //sobrescreve a senha que é diferente no ambiente de teste
            $this->password = '123456';
        }
    }
    
    /**
     * setSoapTimeOut
     * Seta um valor para timeout
     *
     * @param integer $segundos
     */
    public function setSoapTimeOut($segundos = 10)
    {
        if (! empty($segundos)) {
            $this->soapTimeout = $segundos;
            $this->loadSoapClass();
        }
    }
    
    /**
     * getSoapTimeOut
     * Retorna o valor de timeout defido
     *
     * @return integer
     */
    public function getSoapTimeOut()
    {
        return $this->soapTimeout;
    }
    
    /**
     * Grava as mensagens em disco
     *
     * @param string $data conteudo a ser gravado
     * @param string $filename
     * @param int $tpAmb
     * @param string $folder
     * @param string $subFolder
     * @throws RuntimeException
     */
    protected function gravaLog(
        $data,
        $filename,
        $tpAmb = '2',
        $folder = '',
        $subFolder = ''
    ) {
        $anomes = date('Ym');
        $pathTemp = $folder
            . Files\FilesFolders::getAmbiente($tpAmb)
            . DIRECTORY_SEPARATOR
            . $subFolder
            . DIRECTORY_SEPARATOR
            . $anomes;
        if (! Files\FilesFolders::saveFile($pathTemp, $filename, $data)) {
            $msg = 'Falha na gravação no diretório. '.$pathTemp;
            throw new RuntimeException($msg);
        }
    }

    /**
     * Monta as tags com base na chave e no valor do array
     * @param array $data
     * @return string
     */
    protected function addTag($data)
    {
        $ret = '';
        foreach ($data as $key => $value) {
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
            throw new InvalidArgumentException('O limite de 5000 dados foi ultrapassado.');
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
    protected function buildSoapHeader()
    {
        $this->header = "<wsse:Security "
            . "xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\" "
            . "soap:mustUnderstand=\"1\">"
            . "<wsse:UsernameToken "
            . "xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\">"
            . "<wsse:Username>"
            . $this->username
            . "</wsse:Username><wsse:Password "
            . "Type=\"http://docs.oasis-open.org/wss/2004/01/"
            . "oasis-200401-wss-username-token-profile-1.0#PasswordText\">"
            . $this->password
            . "</wsse:Password>"
            . "</wsse:UsernameToken>"
            . "</wsse:Security>";
    }
    
    /**
     * Envia a mensagem para o webservice
     *
     * @param string $urlService
     * @param strting $body
     * @param string $method
     * @return string
     */
    protected function envia($uri, $namespace, $data, $method, $met)
    {
        //constroi a mensagem
        $body = $this->buildMsgH($method, $namespace);
        $body .= $this->buildMsgB($method, $data);
        
        try {
            $retorno = $this->oSoap->send($uri, '', '', $body, $this->xmlns.$method);
        } catch (RuntimeException $e) {
            $msg = $this->oSoap->infoCurl['http_code'] . ' - ' . $this->oSoap->errorCurl;
            throw new RuntimeException($msg);
        }
        $resp = Response::readReturn($met, $retorno);
        //salvar os arquivos para LOG
    }
    
    /**
     * Carrega a classe SOAP e os certificados
     */
    protected function loadSoapClass()
    {
        $this->oSoap = null;
        //if (! is_object($soap)) {
            $soap = new CurlSoap(
                $this->soapTimeout,
                $this->aProxy
            );
        //}
        $this->oSoap = $soap;
    }
}
