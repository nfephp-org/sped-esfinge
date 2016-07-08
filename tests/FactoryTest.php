<?php

namespace NFePHP\Esfinge\Tests;

/**
 * Unit base for others tests
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public $config = '';
    public $pathFixtures;
    
    public function __construct()
    {
        $this->pathFixtures = dirname(__FILE__).DIRECTORY_SEPARATOR."fixtures".DIRECTORY_SEPARATOR;
        $pathFiles = dirname(__FILE__).DIRECTORY_SEPARATOR."fixtures".DIRECTORY_SEPARATOR."Esfinge".DIRECTORY_SEPARATOR;
        $this->config = "{
            \"atualizacao\":\"2016-04-01 09:00:17\",
            \"tpAmb\":2,
            \"username\": \"1006\",
            \"password\": \"123456\",
            \"codigoUnidadeGestora\" => \"1006\",
            \"pathFiles\":\"$pathFiles\",
            \"aProxyConf\":{
                \"proxyIp\":\"\",
                \"proxyPort\":\"\",
                \"proxyUser\":\"\",
                \"proxyPass\":\"\"
            }
        }";
    }
    
    public function testBase()
    {
        $this->assertTrue(true);
    }
}
