<?php


namespace Brera\Lib\Queue;


use Instantiator\Exception\InvalidArgumentException;

class ProducerChannel implements ProducerChannelInterface
{
    /**
     * @var Factory
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
        $this->adapter->initializeProducerChannel($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->channelName;
    }

    /**
     * @param string|OutgoingMessageInterface $outgoingMessage
     */
    public function sendMessage($outgoingMessage)
    {
        $outgoingMessage = $this->getOutgoingMessageFromPayload($outgoingMessage);
        $this->validateOutgoingMessageType($outgoingMessage);
        $this->adapter->sendMessage($this, $outgoingMessage);
    }

    /**
     * @param mixed $payload
     * @return OutgoingMessageInterface
     */
    private function getOutgoingMessageFromPayload($payload)
    {
        if (is_string($payload)) {
            $payload = $this->createOutgoingMessage($payload);
        }
        return $payload;
    }

    /**
     * @param string $payload
     * @return OutgoingMessageInterface
     */
    public function createOutgoingMessage($payload)
    {
        $outgoingMessage = $this->factory->getOutgoingMessage($this, $payload);
        return $outgoingMessage;
    }

    /**
     * @param mixed $payload
     */
    private function validateOutgoingMessageType($payload)
    {
        if (! is_object($payload) || ! $payload instanceof OutgoingMessageInterface) {
            throw new InvalidArgumentException('Payload for sendMessage must be string or implement OutgoingMessageInterface.');
        }
    }
} 