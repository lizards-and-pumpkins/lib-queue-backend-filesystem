<?php

namespace Brera\Lib\Queue\Backend\File;

class FileProducerBackend extends FileAbstractBackend
{
    public function getNewMessageIdentifier($channelName)
    {
        $globPattern = $this->getMessageStateDir($channelName, '*');

        $fileName = $this->file->getNewBaseFilename();
        $fileName = $this->file->getUniqueFilename($globPattern, $fileName);
        $filePath = $this->getMessageStateDir($channelName, self::STATE_PENDING) . DIRECTORY_SEPARATOR . $fileName;

        return $filePath;
    }

    public function writeMessage($messageIdentifier, $data)
    {
        $this->file->writeFile($messageIdentifier, $data);
    }
}
