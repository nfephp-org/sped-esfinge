<?php

namespace NFePHP\Esfinge\Tests;

/**
 * Unit for tests
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use NFePHP\Esfinge\Tools;
use NFePHP\Esfinge\Soap\CurlSoap;
use NFePHP\Esfinge\Tests\FactoryTest;

class ToolsTest extends FactoryTest
{
    
    /**
     * @covers NFePHP\Esfinge\Tools::__construct
     * @expectedException InvalidArgumentException
     */
    public function testInstantiableFail()
    {
        $evt = new Tools('');
        $this->assertInstanceOf(Tools::class, $evt);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::__construct
     */
    public function testInstantiableSuccess()
    {
        $evt = new Tools($this->config);
        $this->assertInstanceOf(Tools::class, $evt);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::setCompetencia
     * @covers NFePHP\Esfinge\Tools::getCompetencia
     */
    public function testSetCompetencia()
    {
        $evt = new Tools($this->config);
        $evt->setCompetencia('201602');
        $this->assertEquals('201602', $evt->getCompetencia());
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::setCompetencia
     * @covers NFePHP\Esfinge\Tools::getCompetencia
     * @expectedException InvalidArgumentException
     */
    public function testSetCompetenciaFailValor()
    {
        $evt = new Tools($this->config);
        $evt->setCompetencia('A201602');
    }

    /**
     * @covers NFePHP\Esfinge\Tools::setCompetencia
     * @covers NFePHP\Esfinge\Tools::getCompetencia
     * @expectedException InvalidArgumentException
     */
    public function testSetCompetenciaFailBimestre()
    {
        $evt = new Tools($this->config);
        $evt->setCompetencia('201612');
    }
    
    
    public function testTokenO()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseObterToken.xml"));
        //$evt = new Tools($this->config, $soap);
        //$resp = $evt->token($evt::TK_O);
        
    }
    
    public function testTokenS()
    {
        
    }
    
    public function testTokenI()
    {
        
    }
    
    public function testTokenF()
    {
        
    }
    
    public function testTokenC()
    {
        
    }

    public function testServidorEnviar()
    {
        
    }
    
    public function testServidorListar()
    {
        
    }
    
    public function testSituacaoServidorFolhaPagamentoEnviar()
    {
        
    }
    
    public function testSituacaoServidorFolhaPagamentoListar()
    {
        
    }
    
    public function testFolhaPagamentoEnviar()
    {
        
    }
    
    public function testFolhaPagamentoListar()
    {
        
    }
    
    public function testComponentesFolhaPagamentoEnviar()
    {
        
    }
    
    public function testComponentesFolhaPagamentoListar()
    {
        
    }

    
}
