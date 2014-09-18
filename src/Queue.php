<?php


namespace Brera\Lib\Queue;


class Queue implements QueueInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var BackendAdapterInterface
     */
    private $adapter;
    
    private $producerChannels = array();
    
    private $consumerChannels = array();
    
    public function __construct(FactoryInterface $factory, BackendAdapterInterface $adapter)
    {
        $this->factory = $factory;
        $this->adapter = $adapter;
    }

    /**
     * @param string $channelName
     * @return ProducerChannelInterface
     */
    public function getProducerChannel($channelName)
    {
        if (! isset($this->producerChannels[$channelName])) {
            $this->producerChannels[$channelName] = $this->factory->getProducerChannel($channelName);
        }
        return $this->producerChannels[$channelName];
    }

    /**
     * @param string $channelName
     * @return ConsumerChannelInterface
     */
    public function getConsumerChannel($channelName)
    {
        if (! isset($this->consumerChannels[$channelName])) {
            $this->consumerChannels[$channelName] = $this->factory->getConsumerChannel($channelName);
        }
        return $this->consumerChannels[$channelName];
    }
} 