<?php

namespace NFePHP\Esfinge\Files;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class FilesFolders
{
    protected static $filesystem;
    
    protected static function init($path)
    {
        $adapter = new Local($path, LOCK_EX, Local::DISALLOW_LINKS, [
            'file' => [
                'public' => 0755,
                'private' => 0700
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0700
            ]
        ]);
        self::$filesystem = new Filesystem($adapter);
    }
    
    public static function save($path, $filename, $contents)
    {
        self::init($path);
        self::$filesystem->put($filename, $contents, ['visibility' => 'public']);
    }
}
