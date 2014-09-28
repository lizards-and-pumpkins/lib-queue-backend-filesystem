<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\AbstractBackendFactory,
    Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    Brera\Lib\Queue\Backend\File\Filesystem\File;

class FileFactory extends AbstractBackendFactory
{
    /**
     * @var FileConfig
     */
    protected $configuredBackendConfigInstance;

    protected function getBackendConfigClass()
    {
        return 'Brera\Lib\Queue\Backend\File\FileConfig';
    }

    protected function getConsumerAdapterClass()
    {
        return 'Brera\Lib\Queue\Backend\File\FileConsumerAdapter';
    }

    protected function getProducerAdapterClass()
    {
        return 'Brera\Lib\Queue\Backend\File\FileProducerAdapter';
    }

    public function getFilesystemDirectoryInstance()
    {
        return new Directory();
    }

    public function getFilesystemFileInstance()
    {
        return new File();
    }

    public function getProducerBackend()
    {
        $directory = $this->getFilesystemDirectoryInstance();
        $file = $this->getFilesystemFileInstance();

        return new FileProducerBackend($this->configuredBackendConfigInstance, $directory, $file);
    }

    public function getConsumerBackend()
    {
        $directory = $this->getFilesystemDirectoryInstance();
        $file = $this->getFilesystemFileInstance();
        $messageBuilder = $this->factory->getMessageBuilder();

        return new FileConsumerBackend($this->configuredBackendConfigInstance, $messageBuilder, $directory, $file);
    }
}
