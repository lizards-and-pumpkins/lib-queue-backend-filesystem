<?php


namespace Brera\Lib\Queue\Factory;


use Brera\Lib\Queue\Backend\File\FileFactory;

class FileQueueFactory extends AbstractQueueFactory
{
    public function getNewBackendFactory()
    {
        return new FileFactory($this);
    }
} 