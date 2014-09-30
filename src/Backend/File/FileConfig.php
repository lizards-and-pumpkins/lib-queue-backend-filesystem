<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\BackendConfigInterface;

class FileConfig implements BackendConfigInterface
{
    protected $storageRootStorage;

    protected $keepProcessedMessages;

    public function setStorageRootDir($directoryPath)
    {
        $this->storageRootStorage = $directoryPath;
    }

    public function getStorageRootDir()
    {
        return $this->storageRootStorage;
    }

    public function setKeepProcessedMessages($flag)
    {
        $this->keepProcessedMessages = boolval($flag);
    }

    public function getKeepProcessedMessages()
    {
        return boolval($this->keepProcessedMessages);
    }
}
