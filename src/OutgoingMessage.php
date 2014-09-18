<?php


namespace Brera\Lib\Queue;


class OutgoingMessage implements OutgoingMessageInterface
{
    /**
     * @var ProducerChannelInterface
     */
    private $channel;

    /**
     * @var BackendFactoryInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $payload;
    
    /**
     * @param ProducerChannelInterface $channel
     * @param BackendAdapterInterface $adapter
     * @param string $payload
     */
    public function __construct(ProducerChannelInterface $channel, BackendAdapterInterface $adapter, $payload)
    {
        $this->channel = $channel;
        $this->adapter = $adapter;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }
} 