<?php


namespace Brera\Lib\Queue;


class ConsumerChannel implements ConsumerChannelInterface
{
    /**
     * @var FactoryInterface $factory
     */
    private $factory;
    
    /**
     * @var BackendAdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $channelName;
    
    /**
     * @param FactoryInterface $factory
     * @param BackendAdapterInterface $adapter
     * @param string $channelName
     */
    public function __construct(FactoryInterface $factory, BackendAdapterInterface $adapter, $channelName)
    {
        $this->factory = $factory;
        $this->adapter = $adapter;
        $this->channelName = $channelName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->channelName;
    }

    /**
     * @return IncomingMessageInterface
     */
    public function receiveMessage()
    {
        $payload = $this->adapter->receiveMessage($this);
        $incomingMessage = $this->factory->getIncomingMessage($this, $payload);
        return $incomingMessage;
    }
} 