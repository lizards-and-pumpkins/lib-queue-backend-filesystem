<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\BackendConfigInterface;

class FileConfig implements BackendConfigInterface
{
    protected $storageRootStorage;

    public function setStorageRootDir($directoryPath)
    {
        $this->storageRootStorage = $directoryPath;

        return $this;
    }

    public function getStorageRootDir()
    {
        return $this->storageRootStorage;
    }
}
