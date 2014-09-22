<?php


namespace Brera\Lib\Queue;


class ConsumerQueue implements ConsumerQueueInterface
{
    /**
     * @var ConsumerAdapterInterface
     */
    private $adapter;
    
    public function __construct(ConsumerAdapterInterface $consumerAdapter)
    {
        $this->adapter = $consumerAdapter;
    }

    /**
     * @param string $channelName
     * @return IncomingMessageInterface
     */
    public function receiveMessageFromChannel($channelName)
    {
        return $this->adapter->receiveMessageFromChannel($channelName);
    }

    /**
     * @param IncomingMessageInterface $message
     */
    public function setMessageAsProcessed(IncomingMessageInterface $message)
    {
        return $this->adapter->setMessageAsProcessed($message);
    }

    /**
     * @param IncomingMessageInterface $message
     * @return string
     */
    public function getMessagePayload(IncomingMessageInterface $message)
    {
        return $message->getPayload();
    }

} 