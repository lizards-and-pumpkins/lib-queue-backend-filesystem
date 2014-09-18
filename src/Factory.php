<?php


namespace Brera\Lib\Queue;


class Factory implements FactoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    function __construct()
    {
        $defaultRepository = $this->getNewRepository();
        $this->setRepository($defaultRepository);
    }

    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return RepositoryInterface
     */
    public function getNewRepository()
    {
        return new Repository($this);
    }

    /**
     * @return ConfigInterface
     */
    public function getSoleConfigInstance()
    {
        return $this->repository->getConfig();
    }

    /**
     * @return ConfigInterface
     */
    public function getNewConfig()
    {
        return new Config();
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return new Queue($this, $this->repository->getBackendAdapter());
    }

    /**
     * @param string $name
     * @return ProducerChannelInterface
     */
    public function getProducerChannel($name)
    {
        return new ProducerChannel($this, $this->repository->getBackendAdapter(), $name);
    }

    /**
     * @param string $name
     * @return ConsumerChannelInterface
     */
    public function getConsumerChannel($name)
    {
        return new ConsumerChannel($this, $this->repository->getBackendAdapter(), $name);
    }

    /**
     * @param ProducerChannelInterface $channel
     * @param string $payload
     * @return OutgoingMessageInterface
     */
    public function getOutgoingMessage(ProducerChannelInterface $channel, $payload)
    {
        return new OutgoingMessage($channel, $this->repository->getBackendAdapter(), $payload);
    }

    /**
     * @param ConsumerChannelInterface $channel
     * @param string $payload
     * @return IncomingMessageInterface
     */
    public function getIncomingMessage(ConsumerChannelInterface $channel, $payload)
    {
        return new IncomingMessage($channel, $this->repository->getBackendAdapter(), $payload);
    }

    /**
     * @return BackendFactoryInterface
     */
    public function getNewBackendFactory()
    {
        $class = $this->repository->getConfiguredBackendFactoryClass();
        /** @var BackendFactoryInterface $factory */
        $factory = new $class($this);
        return $factory;
    }

    /**
     * @return BackendFactoryInterface
     */
    public function getSoleBackendFactoryInstance()
    {
        return $this->repository->getBackendFactory();
    }

    /**
     * @return BackendConfigInterface
     */
    public function getNewBackendConfig()
    {
        return $this->getSoleBackendFactoryInstance()->getNewBackendConfig();
    }

    /**
     * @return BackendConfigInterface
     */
    public function getSoleBackendConfigInstance()
    {
        return $this->repository->getBackendConfig();
    }


    /**
     * @return BackendAdapterInterface
     */
    public function getNewBackendAdapter()
    {
        return $this->getSoleBackendFactoryInstance()->getBackendAdapter();
    }

    /**
     * @return BackendAdapterInterface
     */
    public function getSoleBackendAdapterInstance()
    {
        return $this->repository->getBackendAdapter();
    }
} 