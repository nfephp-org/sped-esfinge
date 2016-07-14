<?php

namespace NFePHP\Esfinge\Tests;

/**
 * Unit Tests for Tools::class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use NFePHP\Esfinge\Tests\FactoryTest;
use NFePHP\Esfinge\Response;

class ResponseTest extends FactoryTest
{
    /**
     *
     */
    public function testObterToken()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseObterToken.xml"));
        $method = 'obterToken';
        $resp = Response::readReturn($method, $xml);
        $jsonR = json_encode($resp);
        //file_put_contents($this->pathFixtures."responseObterToken.txt", $jsonR);
        $expected = str_replace("\n", "", file_get_contents($this->pathFixtures."responseObterToken.txt"));
        $this->assertEquals($expected, $jsonR);
    }
    
    public function testObterSituacaoToken()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseObterSituacaoToken.xml"));
        $method = 'obterSituacaoToken';
        $resp = Response::readReturn($method, $xml);
        $jsonR = json_encode($resp);
        //file_put_contents($this->pathFixtures."responseObterSituacaoToken.txt", $jsonR);
        $expected = str_replace("\n", "", file_get_contents($this->pathFixtures."responseObterSituacaoToken.txt"));
        $this->assertEquals($expected, $jsonR);
    }
    
    public function testIniciarTransferencia()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseIniciarTransferencia.xml"));
        $method = 'iniciarTransferencia';
        $resp = Response::readReturn($method, $xml);
        $jsonR = json_encode($resp);
        //file_put_contents($this->pathFixtures."responseIniciarTransferencia.txt", $jsonR);
        $expected = str_replace("\n", "", file_get_contents($this->pathFixtures."responseIniciarTransferencia.txt"));
        $this->assertEquals($expected, $jsonR);
    }
    
    public function testFinalizarTransferencia()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseFinalizarTransferencia.xml"));
        $method = 'finalizarTransferencia';
        $resp = Response::readReturn($method, $xml);
        $jsonR = json_encode($resp);
        //file_put_contents($this->pathFixtures."responseFinalizarTransferencia.txt", $jsonR);
        $expected = str_replace("\n", "", file_get_contents($this->pathFixtures."responseFinalizarTransferencia.txt"));
        $this->assertEquals($expected, $jsonR);
    }
    
    public function testCancelarTransferencia()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."responseCancelarTransferencia.xml"));
        $method = 'cancelarTransferencia';
        $resp = Response::readReturn($method, $xml);
        $jsonR = json_encode($resp);
        //file_put_contents($this->pathFixtures."responseCancelarTransferencia.txt", $jsonR);
        $expected = str_replace("\n", "", file_get_contents($this->pathFixtures."responseCancelarTransferencia.txt"));
        $this->assertEquals($expected, $jsonR);
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
    
    public function testComponentesFolhaPagamentoEnviar()
    {
    }
    
    public function testComponentesFolhaPagamentoListar()
    {
        $xml = str_replace("\n", "", file_get_contents($this->pathFixtures."componentesFolhaPagamentoL_response.xml"));
        $method = 'listar';
        $resp = Response::readReturn($method, $xml);
        $jsonR = json_encode($resp);
        //file_put_contents($this->pathFixtures."componentesFolhaPagamentoL_response.txt", $jsonR);
        $expected = str_replace("\n", "", file_get_contents($this->pathFixtures."componentesFolhaPagamentoL_response.txt"));
        $this->assertEquals($expected, $jsonR);
    }
    
    public function testFolhaPagamentoEnviar()
    {
    }
    
    public function testFolhaPagamentoListar()
    {
    }
}
