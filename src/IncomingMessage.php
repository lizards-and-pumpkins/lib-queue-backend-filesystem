<?php


namespace Brera\Lib\Queue;


class IncomingMessage implements IncomingMessageInterface
{
    /**
     * @var ConsumerChannelInterface
     */
    private $channel;
    
    /**
     * @var BackendAdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var mixed
     */
    private $identifier;
    
    /**
     * @param ConsumerChannelInterface $channel
     * @param BackendAdapterInterface $adapter
     * @param string $payload
     */
    public function __construct(ConsumerChannelInterface $channel, BackendAdapterInterface $adapter, $payload)
    {
        $this->channel = $channel;
        $this->adapter = $adapter;
        $this->payload = $payload;
    }

    /**
     * @param mixed $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function setAsProcessed()
    {
        $this->adapter->confirmMessageIsProcessed($this->channel, $this);
    }
} 