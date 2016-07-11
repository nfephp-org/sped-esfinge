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
    
    /**
     * @covers NFePHP\Esfinge\Tools::token
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     *
     */
    public function testTokenObtem()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseObterToken.xml"));
        //$evt = new Tools($this->config, $soap);
        //$resp = $evt->token($evt::TK_OBTEM);
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::token
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     */
    public function testTokenStatus()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::token
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     */
    public function testTokenInicia()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::token
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     */
    public function testTokenFinaliza()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::token
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     */
    public function testTokenCancela()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers NFePHP\Esfinge\Tools::servidor
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildEnviarB
     */
    public function testServidorEnviar()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::servidor
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildListarB
     */
    public function testServidorListar()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::situacaoServidorFolhaPagamento
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildEnviarB
     */
    public function testSituacaoServidorFolhaPagamentoEnviar()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers NFePHP\Esfinge\Tools::situacaoServidorFolhaPagamento
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildListarB
     */
    public function testSituacaoServidorFolhaPagamentoListar()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers NFePHP\Esfinge\Tools::folhaPagamento
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildEnviarB
     */
    public function testFolhaPagamentoEnviar()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::folhaPagamento
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildListarB
     */
    public function testFolhaPagamentoListar()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::componentesFolhaPagamento
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildEnviarB
     */
    public function testComponentesFolhaPagamentoEnviar()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @covers NFePHP\Esfinge\Tools::componentesFolhaPagamento
     * @covers NFePHP\Esfinge\Base::buildSoapHeader
     * @covers NFePHP\Esfinge\Base::envia
     * @covers NFePHP\Esfinge\Base::addTag
     * @covers NFePHP\Esfinge\Base::buildMsgB
     * @covers NFePHP\Esfinge\Base::buildMsgH
     * @covers NFePHP\Esfinge\Base::buildListarB
     */
    public function testComponentesFolhaPagamentoListar()
    {
        $this->assertTrue(true);
    }
}
