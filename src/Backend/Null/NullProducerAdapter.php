<?php


namespace Brera\Lib\Queue\Backend\Null;

use Brera\Lib\Queue\AbstractProducerAdapter;

class NullProducerAdapter extends AbstractProducerAdapter
{
    /**
     * @param string $channelName
     * @param string $payload
     * @return string Message Identifier
     */
    protected function sendMessageToBackend($channelName, $payload)
    {
        // Implement sendMessageToBackend() method for real producer adapters
    }
}