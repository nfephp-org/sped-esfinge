<?php

namespace NFePHP\Esfinge\Tests\Files;

/**
 * Unit base for others tests
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use NFePHP\Esfinge\Files\FilesFolders;

class FilesFoldersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers NFePHP\Esfinge\Files\FilesFolders::save
     * @covers NFePHP\Esfinge\Files\FilesFolders::init
     */
    public function testFilesFoldersSave()
    {
        $path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."fixtures".DIRECTORY_SEPARATOR.'homologacao';
        $filename = date('Ym').DIRECTORY_SEPARATOR.'teste.txt';
        $data = 'Teste de gravação de arquivo.';
        FilesFolders::save($path, $filename, $data);
        $this->assertFileExists($path);
        unlink($path.DIRECTORY_SEPARATOR.$filename);
        rmdir($path.DIRECTORY_SEPARATOR.date('Ym'));
    }
}
