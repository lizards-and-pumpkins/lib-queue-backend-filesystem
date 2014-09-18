<?php


namespace Brera\Lib\Queue\Backend\Null;

use Brera\Lib\Queue\BackendConfigInterface;
use Brera\Lib\Queue\BackendFactoryInterface;

class NullFactory implements BackendFactoryInterface
{
    /**
     * @var NullConfig
     */
    private $configuredBackendConfigInstance;

    /**
     * Will be called after instantiation. Receives the backend config
     * instance that will be used to configure the queue backend.
     *
     * @param BackendConfigInterface $backendConfig
     */
    public function setConfiguredBackendConfigInstance(BackendConfigInterface $backendConfig)
    {
        $this->configuredBackendConfigInstance = $backendConfig;
    }


    /**
     * @return NullConfig
     */
    public function getNewBackendConfig()
    {
        return new NullConfig();
    }

    /**
     * @return NullAdapter
     */
    public function getBackendAdapter()
    {
        return new NullAdapter($this, $this->configuredBackendConfigInstance);
    }

} 