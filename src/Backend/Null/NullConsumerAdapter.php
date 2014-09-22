<?php


namespace Brera\Lib\Queue\Backend\Null;

use Brera\Lib\Queue\AbstractConsumerAdapter;
use Brera\Lib\Queue\IncomingMessageInterface;

class NullConsumerAdapter extends AbstractConsumerAdapter
{
    /**
     * @param string $channelName
     * @return mixed Internal backend specific message representation
     */
    protected function receiveBackendMessageFromChannel($channelName)
    {
        // Implement receiveBackendMessageFromChannel() for real consumer adapter.
    }

    /**
     * @param mixed $message Internal backend specific message representation
     * @return string
     */
    protected function getPayloadFromBackendMessage($message)
    {
        // Implement getPayloadFromBackendMessage() for real consumer adapter.
        return '';
    }

    /**
     * @param mixed $message Internal backend specific message representation
     * @return mixed
     */
    protected function getIdentifierFromBackendMessage($message)
    {
        // Implement getIdentifierFromBackendMessage() for real consumer adapter.
    }

    /**
     * @param IncomingMessageInterface $message
     */
    public function setMessageAsProcessed(IncomingMessageInterface $message)
    {
        // Implement setMessageAsProcessed() for real consumer adapter.
    }

}