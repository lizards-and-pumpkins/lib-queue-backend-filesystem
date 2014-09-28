<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\AbstractConsumerAdapter,
    Brera\Lib\Queue\IncomingMessageInterface;

class FileConsumerAdapter extends AbstractConsumerAdapter
{
    /**
     * @var FileConsumerBackend
     */
    protected $backendImplementation;

    protected function receiveBackendMessageFromChannel($channelName)
    {
        return $this->backendImplementation->getMessageFromQueue($channelName);
    }

    protected function getPayloadFromBackendMessage($message)
    {
        return $message->getPayload();
    }

    protected function getIdentifierFromBackendMessage($message)
    {
        return $message->getIdentifier();
    }

    public function setMessageAsProcessed(IncomingMessageInterface $message)
    {
        $this->backendImplementation->setMessageAsProcessed($message);
    }
}
