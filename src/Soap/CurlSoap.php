<?php

namespace NFePHP\Esfinge\Soap;

use RuntimeException;
use NFePHP\Esfinge\Files\FilesFolders;

class CurlSoap
{
    /**
     * soapDebug
     * @var string
     */
    public $soapDebug = '';
    /**
     * soapTimeout
     * @var integer
     */
    public $soapTimeout = 10;
    /**
     * lastMsg
     * @var string
     */
    public $lastMsg = '';

    /**
     * errorCurl
     * @var string
     */
    public $errorCurl = '';
    /**
     * infoCurl
     * @var array
     */
    public $infoCurl = array();
    /**
     * proxyIP
     * @var string
     */
    private $proxyIP = '';
    /**
     * proxyPORT
     * @var string
     */
    private $proxyPORT = '';
    /**
     * proxyUSER
     * @var string
     */
    private $proxyUSER = '';
    /**
     * proxyPASS
     * @var string
     */
    private $proxyPASS = '';
    private $pathlog;
    
    public function __construct($pathlog, $timeout, $aproxy)
    {
        $this->pathlog = $pathlog;
        $this->soapTimeout = $timeout;
        $ipNumber = $aproxy['proxyIp'];
        $port = $aproxy['proxyPort'];
        $user = $aproxy['proxyUser'];
        $pass = $aproxy['proxyPass'];
        $this->setProxy($ipNumber, $port, $user, $pass);
    }
    
    /**
     * setProxy
     * Seta o uso do proxy
     * @param string $ipNumber numero IP do proxy server
     * @param string $port numero da porta usada pelo proxy
     * @param string $user nome do usuário do proxy
     * @param string $pass senha de acesso ao proxy
     * @return boolean
     */
    public function setProxy($ipNumber, $port, $user = '', $pass = '')
    {
        $this->proxyIP = $ipNumber;
        $this->proxyPORT = $port;
        $this->proxyUSER = $user;
        $this->proxyPASS = $pass;
    }
    
    /**
     * getProxy
     * Retorna os dados de configuração do Proxy em um array
     * @return array
     */
    public function getProxy()
    {
        $aProxy['ip'] = $this->proxyIP;
        $aProxy['port'] = $this->proxyPORT;
        $aProxy['username'] = $this->proxyUSER;
        $aProxy['password'] = $this->proxyPASS;
        return $aProxy;
    }
    
    /**
     * Envia mensagem ao webservice
     * @param string $urlsevice
     * @param string $namespace
     * @param string $header
     * @param string $body
     * @param string $method
     * @return boolean|string
     */
    public function send($urlservice, $namespace, $header, $body, $method)
    {
        //monta a mensagem ao webservice
        $data = '<?xml version="1.0" encoding="utf-8"?>'.'<soap:Envelope ';
        $data .= 'xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" ';
        $data .= 'xmlns:svc="'.$namespace.'">';
        $data .= '<soap:Header>'.$header.'</soap:Header>';
        $data .= '<soap:Body>'.$body.'</soap:Body>';
        $data .= '</soap:Envelope>';
        $data = $this->clearMsg($data);
        //grava em propriedade da classe o envelope não compactado
        $this->lastMsg = $data;
        //salva o envelope em arquivo para efeito de LOG
        $mark = date('Ym').DIRECTORY_SEPARATOR.date('d_His_').$method;
        $filepath = $mark.'.xml';
        FilesFolders::save($this->pathlog, $filepath, $data);
        //compacta mensagem com GZip
        $data = gzencode($data);
        //tamanho da mensagem
        $tamanho = strlen($data);
        //estabelecimento dos parametros da mensagem
        $parametros = [
            "Accept-Encoding: gzip, deflate",
            "Content-Type: text/xml;charset=UTF-8",
            "Content-encoding: gzip",
            "Content-length: $tamanho"
        ];
        //solicita comunicação via cURL
        $resposta = $this->zCommCurl($urlservice, $data, $parametros);
        if (empty($resposta)) {
            $msg = "Não houve retorno do Curl.\n $this->errorCurl";
            throw new RuntimeException($msg);
        }
        //obtem o bloco html da resposta
        $xPos = stripos($resposta, "\r\n\r\n");
        $blocoHtml = $resposta;
        $decompressPart = '';
        if ($xPos !== false) {
            $blocoHtml = substr($resposta, 0, $xPos);
            $compressPart = substr($resposta, $xPos+4, strlen($resposta)-($xPos+4));
            if (substr($compressPart, 0, 2) == "\x1f\x8b") {
                $decompressPart = trim(gzdecode($compressPart));
            } else {
                $decompressPart = $compressPart;
            }
        }
        if ($this->infoCurl["http_code"] != '200') {
            //se não é igual a 200 houve erro
            $msg = $blocoHtml ."\r\n". $decompressPart;
            $filepath = $mark.'_ERROR.log';
            FilesFolders::save($this->pathlog, $filepath, $msg);
            throw new RuntimeException($msg);
        }
        //localiza a primeira marca de tag
        $xPos = stripos($decompressPart, "<");
        $lenresp = strlen($decompressPart);
        //se não existir não é um xml nem um html
        if ($xPos !== false) {
            $xml = substr($decompressPart, $xPos, $lenresp-$xPos);
        } else {
            $xml = '';
        }
        //$xml = $decompressPart;
        //testa para saber se é um xml mesmo ou é um html
        $result = simplexml_load_string($xml, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
        if ($result === false) {
            //não é um xml então pode limpar
            $xml = '';
        }
        if ($xml == '') {
            $msg = "Não houve retorno de um xml verifique soapDebug!!";
            throw new RuntimeException($msg);
        }
        if ($xml != '' && substr($xml, 0, 5) != '<?xml') {
            $xml = '<?xml version="1.0" encoding="utf-8"?>'.$xml;
        }
        //grava a resposta do TCE/SC
        $filepath = $mark.'_response.xml';
        FilesFolders::save($this->pathlog, $filepath, $xml);
        return $xml;
    }
    
    /**
     * Envio via cURL
     * @param string $url
     * @param string $data
     * @param array $parametros
     * @return string
     */
    protected function zCommCurl($url, $data = '', $parametros = array())
    {
        //incializa cURL
        $oCurl = curl_init();
        //setting da seção soap
        if ($this->proxyIP != '') {
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($oCurl, CURLOPT_PROXY, $this->proxyIP.':'.$this->proxyPORT);
            if ($this->proxyPASS != '') {
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->proxyUSER.':'.$this->proxyPASS);
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            }
        }
        //força a resolução de nomes com IPV4 e não com IPV6, isso
        //pode acelerar temporáriamente as falhas ou demoras decorrentes de
        //ambiente mal preparados como os da SEFAZ GO, porém pode causar
        //problemas no futuro quando os endereços IPV4 deixarem de ser usados
        curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soapTimeout);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 1);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        if (!empty($parametros)) {
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parametros);
        }
        //inicia a conexão
        $resposta = curl_exec($oCurl);
        //obtem as informações da conexão
        $info = curl_getinfo($oCurl);
        //carrega os dados para debug
        $this->zDebug($info, $data, $resposta);
        $this->errorCurl = curl_error($oCurl);
        //fecha a conexão
        curl_close($oCurl);
        //retorna resposta
        return $resposta;
    }
    
    /**
     * zDebug
     * @param array $info
     * @param string $data
     * @param string $resposta
     */
    private function zDebug($info = array(), $data = '', $resposta = '')
    {
        $this->infoCurl["url"] = $info["url"];
        $this->infoCurl["content_type"] = $info["content_type"];
        $this->infoCurl["http_code"] = $info["http_code"];
        $this->infoCurl["header_size"] = $info["header_size"];
        $this->infoCurl["request_size"] = $info["request_size"];
        $this->infoCurl["filetime"] = $info["filetime"];
        $this->infoCurl["ssl_verify_result"] = $info["ssl_verify_result"];
        $this->infoCurl["redirect_count"] = $info["redirect_count"];
        $this->infoCurl["total_time"] = $info["total_time"];
        $this->infoCurl["namelookup_time"] = $info["namelookup_time"];
        $this->infoCurl["connect_time"] = $info["connect_time"];
        $this->infoCurl["pretransfer_time"] = $info["pretransfer_time"];
        $this->infoCurl["size_upload"] = $info["size_upload"];
        $this->infoCurl["size_download"] = $info["size_download"];
        $this->infoCurl["speed_download"] = $info["speed_download"];
        $this->infoCurl["speed_upload"] = $info["speed_upload"];
        $this->infoCurl["download_content_length"] = $info["download_content_length"];
        $this->infoCurl["upload_content_length"] = $info["upload_content_length"];
        $this->infoCurl["starttransfer_time"] = $info["starttransfer_time"];
        $this->infoCurl["redirect_time"] = $info["redirect_time"];
        //coloca as informações em uma variável
        $txtInfo ="";
        foreach ($info as $key => $content) {
            if (is_string($content)) {
                $txtInfo .= strtoupper($key).'='.$content."\n";
            }
        }
        //carrega a variavel debug
        $this->soapDebug = $data."\n\n".$txtInfo."\n".$resposta;
    }
    
    /**
     * clearMsg
     * @param string $msg
     * @return string
     */
    protected function clearMsg($msg)
    {
        $nmsg = str_replace(array(' standalone="no"','default:',':default',"\n","\r","\t"), '', $msg);
        $nnmsg = str_replace('> ', '>', $nmsg);
        if (strpos($nnmsg, '> ')) {
            $nnmsg = $this->clearMsg((string) $nnmsg);
        }
        return $nnmsg;
    }
}
