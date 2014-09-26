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
        $this->backendImplementation->checkIfChannelIsInitialized($channelName);

        $this->backendImplementation->lock();
        $messageIdentifier = $this->backendImplementation->getNextMessageIdentifier($channelName);
        $this->backendImplementation->unlock();

        while ('' === $messageIdentifier) {
            usleep(100000);
            $this->backendImplementation->lock();
            $messageIdentifier = $this->backendImplementation->getNextMessageIdentifier($channelName);
            $this->backendImplementation->unlock();
        }

        $payload = $this->backendImplementation->readMessage($messageIdentifier);

        return array(
            'payload' => $payload,
            'channel' => $channelName,
            'identifier' => $messageIdentifier
        );
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
        $channelName = $message->getChannel();

        $this->backendImplementation->checkIfChannelIsInitialized($channelName);
        $this->backendImplementation->changeMessageState(
            $channelName, $message->getIdentifier(), FileAbstractBackend::STATE_COMPLETED
        );
    }
}
