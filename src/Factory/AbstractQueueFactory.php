<?php


namespace Brera\Lib\Queue\Factory;

use Brera\Lib\Queue\BackendConfigInterface;
use Brera\Lib\Queue\BackendFactoryInterface;
use Brera\Lib\Queue\Config;
use Brera\Lib\Queue\ConsumerQueue;
use Brera\Lib\Queue\FactoryInterface;
use Brera\Lib\Queue\IncomingMessage;
use Brera\Lib\Queue\MessageBuilder;
use Brera\Lib\Queue\OutgoingMessage;
use Brera\Lib\Queue\ProducerQueue;
use Brera\Lib\Queue\Repository;
use Brera\Lib\Queue\RepositoryInterface;

abstract class AbstractQueueFactory implements FactoryInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    function __construct()
    {
        $defaultRepository = $this->getNewRepository();
        $this->setRepository($defaultRepository);
    }

    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getNewRepository()
    {
        return new Repository($this);
    }

    /**
     * @return Config
     */
    public function getRegisteredConfigInstance()
    {
        return $this->repository->getConfig();
    }

    public function getNewConfig()
    {
        return new Config();
    }

    public function getConsumerQueue()
    {
        return new ConsumerQueue($this->repository->getConsumerAdapter());
    }

    public function getProducerQueue()
    {
        return new ProducerQueue($this->repository->getProducerAdapter());
    }

    /**
     * @param string $channelName
     * @param string $payload
     * @param mixed $identifier
     * @return OutgoingMessage
     */
    public function getOutgoingMessage($channelName, $payload, $identifier)
    {
        return new OutgoingMessage($channelName, $payload, $identifier);
    }

    /**
     * @return MessageBuilder
     */
    public function getMessageBuilder()
    {
        return new MessageBuilder($this);
    }

    /**
     * @param string $channelName
     * @param string $payload
     * @param mixed $identifier
     * @return IncomingMessage
     */
    public function getIncomingMessage($channelName, $payload, $identifier)
    {
        return new IncomingMessage($channelName, $payload, $identifier);
    }

    /**
     * @return BackendFactoryInterface
     */
    public function getRegisteredBackendFactoryInstance()
    {
        return $this->repository->getBackendFactory();
    }

    /**
     * @return BackendConfigInterface
     */
    public function getRegisteredBackendConfigInstance()
    {
        return $this->repository->getBackendConfig();
    }
} 