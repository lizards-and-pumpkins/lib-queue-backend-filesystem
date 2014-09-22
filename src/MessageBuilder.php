<?php


namespace Brera\Lib\Queue;


class MessageBuilder implements MessageBuilderInterface
{
    private $factory;
    private $identifier;
    private $payload;
    private $channel;
    
    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param string $channelName
     */
    public function setChannel($channelName)
    {
        $this->channel = $channelName;
    }

    /**
     * @param MessageInterface $message
     */
    public function initializeFromMessage(MessageInterface $message)
    {
        $this->setIdentifier($message->getIdentifier());
        $this->setPayload($message->getPayload());
        $this->setChannel($message->getChannel());
    }

    /**
     * @return IncomingMessageInterface
     */
    public function getIncomingMessage()
    {
        return $this->factory->getIncomingMessage($this->channel, $this->payload, $this->identifier);
    }

    /**
     * @return OutgoingMessageInterface
     */
    public function getOutgoingMessage()
    {
        return $this->factory->getOutgoingMessage($this->channel, $this->payload, $this->identifier);
    }
} 