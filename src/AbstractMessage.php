<?php

namespace Brera\Lib\Queue;


abstract class AbstractMessage implements MessageInterface
{
    protected $channelName;
    protected $payload;
    protected $identifier;
    
    /**
     * @param string $channelName
     * @param string $payload
     * @param mixed $identifier
     */
    public function __construct($channelName, $payload, $identifier)
    {
        $this->channelName = $channelName;
        $this->payload = $payload;
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

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channelName;
    }
} 