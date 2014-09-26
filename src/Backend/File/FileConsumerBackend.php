<?php

namespace Brera\Lib\Queue\Backend\File;

class FileConsumerBackend extends FileAbstractBackend
{
    public function getNextMessageIdentifier($channelName)
    {
        $messageIdentifier = '';
        $pendingStateDir = $this->getMessageStateDir($channelName, self::STATE_PENDING);

        if ($fileName = $this->directory->getNameOfOldestFileInDir($pendingStateDir)) {
            $currentIdentifier = $pendingStateDir . DIRECTORY_SEPARATOR . $fileName;
            $this->changeMessageState($channelName, $currentIdentifier, self::STATE_PROCESSING);

            $processingStateDir = $this->getMessageStateDir($channelName, self::STATE_PROCESSING);
            $messageIdentifier = $processingStateDir . DIRECTORY_SEPARATOR . $fileName;
        }

        return $messageIdentifier;
    }

    public function readMessage($messageIdentifier)
    {
        $result = $this->file->readFile($messageIdentifier);

        return $result;
    }
}
