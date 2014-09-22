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
     * @return IncomingMessageInterface
     */
    public function getIncomingMessage($channelName, $payload, $identifier)
    {
        return new IncomingMessage($channelName, $payload, $identifier);
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
    public function getRegisteredBackendFactoryInstance()
    {
        return $this->repository->getBackendFactory();
    }

    /**
     * @return BackendConfigInterface
     */
    public function getNewBackendConfig()
    {
        return $this->getRegisteredBackendFactoryInstance()->getNewBackendConfig();
    }

    /**
     * @return BackendConfigInterface
     */
    public function getRegisteredBackendConfigInstance()
    {
        return $this->repository->getBackendConfig();
    }
} 