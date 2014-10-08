<?php


namespace Brera\Lib\Queue;


use Brera\Lib\Queue\Factory\AbstractQueueFactory;

class Repository implements RepositoryInterface
{
    /**
     * @var AbstractQueueFactory
     */
    private $factory;
    private $backendFactory;
    private $producerAdapter;
    private $consumerAdapter;
    private $backendConfig;
    
    
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    /**
     * @return BackendFactoryInterface
     */
    public function getBackendFactory()
    {
        if (! isset($this->backendFactory)) {
            $this->backendFactory = $this->factory->getNewBackendFactory();
        }
        return $this->backendFactory;
    }

    /**
     * @return ProducerAdapterInterface
     */
    public function getProducerAdapter()
    {
        if (! isset($this->producerAdapter)) {
            $this->producerAdapter = $this->getBackendFactory()->getProducerAdapter();
        }
        return $this->producerAdapter;
    }

    /**
     * @return ConsumerAdapterInterface
     */
    public function getConsumerAdapter()
    {
        if (! isset($this->consumerAdapter)) {
            $this->consumerAdapter = $this->getBackendFactory()->getConsumerAdapter();
        }
        return $this->consumerAdapter;
    }

    /**
     * @return BackendConfigInterface
     */
    public function getBackendConfig()
    {
        if (! isset($this->backendConfig)) {
            $this->backendConfig = $this->getBackendFactory()->getNewBackendConfig();
            $this->getBackendFactory()->setConfiguredBackendConfigInstance($this->backendConfig);
        }
        return $this->backendConfig;
    }
} 