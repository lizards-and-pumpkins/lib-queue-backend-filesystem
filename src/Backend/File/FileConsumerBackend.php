<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    Brera\Lib\Queue\Backend\File\Filesystem\File,
    Brera\Lib\Queue\IncomingMessageInterface;

class FileConsumerBackend extends FileAbstractBackend
{
    public function __construct(FileConfig $config, Directory $directory, File $file)
    {
        $this->config = $config;
        $this->directory = $directory;
        $this->file = $file;
    }

    public function getMessageFromQueue($channelName)
    {
        $this->checkIfChannelIsInitialized($channelName);

        $messageIdentifier = $this->getNextMessageIdentifier($channelName);

        while ('' === $messageIdentifier) {
            usleep(100000);
            $messageIdentifier = $this->getNextMessageIdentifier($channelName);
        }

        $payload = $this->readMessage($messageIdentifier);

        return array(
            'channel'       => $channelName,
            'payload'       => $payload,
            'identifier'    => $messageIdentifier
        );
    }

    public function setMessageAsProcessed(IncomingMessageInterface $message)
    {
        $channelName = $message->getChannel();
        $this->checkIfChannelIsInitialized($channelName);
        if ($this->config->getKeepProcessedMessages()) {
            $this->changeMessageState($channelName, $message->getIdentifier(), FileAbstractBackend::STATE_COMPLETED);
        } else {
            $this->file->removeFile($message->getIdentifier());
        }
    }

    protected function getNextMessageIdentifier($channelName)
    {
        $messageIdentifier = '';
        $pendingStateDir = $this->getMessageStateDir($channelName, self::STATE_PENDING);

        $this->file->lock($this->lockFileHandle);

        if ($fileName = $this->directory->getNameOfFirstFileInSortedDir($pendingStateDir)) {
            $currentIdentifier = $pendingStateDir . DIRECTORY_SEPARATOR . $fileName;
            $this->changeMessageState($channelName, $currentIdentifier, self::STATE_PROCESSING);

            $processingStateDir = $this->getMessageStateDir($channelName, self::STATE_PROCESSING);
            $messageIdentifier = $processingStateDir . DIRECTORY_SEPARATOR . $fileName;
        }

        $this->file->unlock($this->lockFileHandle);

        return $messageIdentifier;
    }

    protected function readMessage($messageIdentifier)
    {
        $result = $this->file->readFile($messageIdentifier);

        return $result;
    }
}
