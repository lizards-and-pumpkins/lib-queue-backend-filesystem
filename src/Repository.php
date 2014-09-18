<?php


namespace Brera\Lib\Queue;


class Repository implements RepositoryInterface
{
    /**
     * @var Factory
     */
    private $factory;
    private $config;
    private $queue;
    private $backendFactory;
    private $backendAdapter;
    private $backendConfig;
    
    
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig()
    {
        if (! isset($this->config)) {
            $this->config = $this->factory->getNewConfig();
        }
        return $this->config;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        if (! isset($this->queue)) {
            $this->queue = $this->factory->getQueue();
        }
        return $this->queue;
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
     * @return BackendAdapterInterface
     */
    public function getBackendAdapter()
    {
        if (! isset($this->backendAdapter)) {
            $this->backendAdapter = $this->factory->getNewBackendAdapter();
        }
        return $this->backendAdapter;
    }

    /**
     * @return BackendConfigInterface
     */
    public function getBackendConfig()
    {
        if (! isset($this->backendConfig)) {
            $this->backendConfig = $this->factory->getNewBackendConfig();
            $this->getBackendFactory()->setConfiguredBackendConfigInstance($this->backendConfig);
        }
        return $this->backendConfig;
    }

    /**
     * @return string
     */
    public function getConfiguredBackendFactoryClass()
    {
        return $this->getConfig()->getBackendFactoryClass();
    }
} 