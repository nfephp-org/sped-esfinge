<?php

namespace NFePHP\Esfinge\Tests;

/**
 * Unit for tests
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use NFePHP\Esfinge\Base;
use NFePHP\Esfinge\Tests\FactoryTest;

class BaseTest extends FactoryTest
{
    
    /**
     * @covers NFePHP\Esfinge\Base::__construct
     * @covers NFePHP\Esfinge\Base::loadSoapClass
     */
    public function testInstantiable()
    {
        $evt = new Base($this->config);
        $this->assertInstanceOf(Base::class, $evt);
    }
    
    /**
     * @covers NFePHP\Esfinge\Base::__construct
     * @expectedException InvalidArgumentException
     */
    public function testInstantiableFail()
    {
        $evt = new Base('');
        $this->assertInstanceOf(Base::class, $evt);
    }
    
    /**
     * @covers NFePHP\Esfinge\Base::setAmbiente
     * @covers NFePHP\Esfinge\Base::getAmbiente
     */
    public function testSetAmbiente()
    {
        $evt = new Base($this->config);
        $evt->setAmbiente(1);
        $expected = 1;
        $actual = $evt->getAmbiente();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers NFePHP\Esfinge\Base::setAmbiente
     * @covers NFePHP\Esfinge\Base::getAmbiente
     */
    public function testSetAmbienteIncorret()
    {
        $evt = new Base($this->config);
        $evt->setAmbiente('Incorrect');
        $expected = 2;
        $actual = $evt->getAmbiente();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers NFePHP\Esfinge\Base::getAmbiente
     */
    public function testGetAmbiente()
    {
        $evt = new Base($this->config);
        $expected = 2;
        $actual = $evt->getAmbiente();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @covers NFePHP\Esfinge\Base::setSoapTimeOut
     * @covers NFePHP\Esfinge\Base::getSoapTimeOut
     * @covers NFePHP\Esfinge\Base::loadSoapClass
     */
    public function testSetSoapTimeout()
    {
        $evt = new Base($this->config);
        $evt->setSoapTimeOut(100);
        $expected = 100;
        $actual = $evt->getSoapTimeOut();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers NFePHP\Esfinge\Base::setSoapTimeOut
     * @covers NFePHP\Esfinge\Base::getSoapTimeOut
     * @covers NFePHP\Esfinge\Base::loadSoapClass
     */
    public function testSetSoapTimeoutIncorrect()
    {
        $evt = new Base($this->config);
        $evt->setSoapTimeOut('Incorrect');
        $expected = 10;
        $actual = $evt->getSoapTimeOut();
        $this->assertEquals($expected, $actual);
    }
}
