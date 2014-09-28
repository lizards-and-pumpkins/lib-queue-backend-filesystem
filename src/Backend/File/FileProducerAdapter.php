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
        return $this->backendImplementation->addMessageToQueue($channelName, $payload);
    }
}
