<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    Brera\Lib\Queue\Backend\File\Filesystem\File,
    Brera\Lib\Queue\MessageBuilder,
    Brera\Lib\Queue\IncomingMessageInterface;

class FileConsumerBackend extends FileAbstractBackend
{
    private $messageBuilder;

    public function __construct(FileConfig $config, MessageBuilder $messageBuilder, Directory $directory, File $file)
    {
        $this->config = $config;
        $this->messageBuilder = $messageBuilder;
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

        $this->messageBuilder->setChannel($channelName);
        $this->messageBuilder->setPayload($payload);
        $this->messageBuilder->setIdentifier($messageIdentifier);

        return $this->messageBuilder->getIncomingMessage();
    }

    public function setMessageAsProcessed(IncomingMessageInterface $message)
    {
        $channelName = $message->getChannel();
        $this->checkIfChannelIsInitialized($channelName);
        $this->changeMessageState($channelName, $message->getIdentifier(), FileAbstractBackend::STATE_COMPLETED);
    }

    protected function getNextMessageIdentifier($channelName)
    {
        $messageIdentifier = '';
        $pendingStateDir = $this->getMessageStateDir($channelName, self::STATE_PENDING);

        $this->file->lock($this->lockFilePointer);

        if ($fileName = $this->directory->getNameOfOldestFileInDir($pendingStateDir)) {
            $currentIdentifier = $pendingStateDir . DIRECTORY_SEPARATOR . $fileName;
            $this->changeMessageState($channelName, $currentIdentifier, self::STATE_PROCESSING);

            $processingStateDir = $this->getMessageStateDir($channelName, self::STATE_PROCESSING);
            $messageIdentifier = $processingStateDir . DIRECTORY_SEPARATOR . $fileName;
        }

        $this->file->unlock($this->lockFilePointer);

        return $messageIdentifier;
    }

    protected function readMessage($messageIdentifier)
    {
        $result = $this->file->readFile($messageIdentifier);

        return $result;
    }
}
