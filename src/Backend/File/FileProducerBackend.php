<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    Brera\Lib\Queue\Backend\File\Filesystem\File;

class FileProducerBackend extends FileAbstractBackend
{
    public function __construct(FileConfig $config, Directory $directory, File $file)
    {
        $this->config = $config;
        $this->directory = $directory;
        $this->file = $file;
    }

    public function addMessageToQueue($channelName, $payload)
    {
        $this->checkIfChannelIsInitialized($channelName);

        $this->file->lock($this->lockFileHandle);

        $messageIdentifier = $this->getNewMessageIdentifier($channelName);
        $this->writeMessage($messageIdentifier, $payload);

        $this->file->unlock($this->lockFileHandle);

        return $messageIdentifier;
    }

    protected function getNewMessageIdentifier($channelName)
    {
        $globPattern = $this->getMessageStateDir($channelName, '*');

        $fileName = $this->file->getNewBaseFilename();
        $fileName = $this->file->getUniqueFilename($globPattern, $fileName);
        $filePath = $this->getMessageStateDir($channelName, self::STATE_PENDING) . DIRECTORY_SEPARATOR . $fileName;

        return $filePath;
    }

    protected function writeMessage($messageIdentifier, $data)
    {
        $this->file->writeFile($messageIdentifier, $data);
    }
}
