<?php


namespace Brera\Lib\Queue;


class ProducerQueue implements ProducerQueueInterface
{
    /**
     * @var ProducerAdapterInterface
     */
    private $adapter;
    
    public function __construct(ProducerAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $channelName
     * @param string $payload
     * @return OutgoingMessageInterface
     */
    public function sendMessageByChannel($channelName, $payload)
    {
        return $this->adapter->sendMessage($channelName, $payload);
    }

    /**
     * @param OutgoingMessageInterface $message
     * @return string
     */
    public function getMessagePayload(OutgoingMessageInterface $message)
    {
        return $message->getPayload();
    }

} 