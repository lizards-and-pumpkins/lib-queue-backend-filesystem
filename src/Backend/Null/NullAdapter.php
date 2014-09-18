<?php


namespace Brera\Lib\Queue\Backend\Null;

use Brera\Lib\Queue\BackendAdapterInterface;
use Brera\Lib\Queue\ConsumerChannelInterface;
use Brera\Lib\Queue\IncomingMessageInterface;
use Brera\Lib\Queue\OutgoingMessageInterface;
use Brera\Lib\Queue\ProducerChannelInterface;

class NullAdapter implements BackendAdapterInterface
{
    public function initialize()
    {
        // Purposely left empty for null adapter.
    }

    public function initializeProducerChannel(ProducerChannelInterface $channel)
    {
        // Purposely left empty for null adapter.
    }

    public function initializeConsumerChannel(ConsumerChannelInterface $channel)
    {
        // Purposely left empty for null adapter.
    }

    /**
     * @param ConsumerChannelInterface $channel
     * @return string
     */
    public function receiveMessage(ConsumerChannelInterface $channel)
    {
        return '';
    }

    /**
     * @param ProducerChannelInterface $channel
     * @param OutgoingMessageInterface $outgoingMessage
     */
    public function sendMessage(ProducerChannelInterface $channel, OutgoingMessageInterface $outgoingMessage)
    {
        // Purposely left empty for null adapter.
    }

    /**
     * @param ConsumerChannelInterface $channel
     * @param IncomingMessageInterface $message
     */
    public function confirmMessageIsProcessed(ConsumerChannelInterface $channel, IncomingMessageInterface $message)
    {
        // Purposely left empty for null adapter.
    }
} 