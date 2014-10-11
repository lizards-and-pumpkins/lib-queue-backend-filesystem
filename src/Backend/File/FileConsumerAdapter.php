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
        return $message['payload'];
    }

    protected function getIdentifierFromBackendMessage($message)
    {
        return $message['identifier'];
    }

    public function setMessageAsProcessed(IncomingMessageInterface $message)
    {
        $this->backendImplementation->setMessageAsProcessed($message);
    }
}
