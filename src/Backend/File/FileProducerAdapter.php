<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\AbstractProducerAdapter;

class FileProducerAdapter extends AbstractProducerAdapter
{
    /**
     * @var FileProducerBackend
     */
    protected $backendImplementation;

    public function sendMessageToBackend($channelName, $payload)
    {
        $this->backendImplementation->checkIfChannelIsInitialized($channelName);

        $this->backendImplementation->lock();

        $messageIdentifier = $this->backendImplementation->getNewMessageIdentifier($channelName);
        $this->backendImplementation->writeMessage($messageIdentifier, $payload);

        $this->backendImplementation->unlock();

        return $messageIdentifier;
    }
}
