<?php


namespace Brera\Lib\Queue\Backend\Null;

use Brera\Lib\Queue\BackendFactoryInterface;
use Brera\Lib\Queue\FactoryInterface;

class NullFactory implements BackendFactoryInterface
{
    private $factory;
    
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
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
        return new NullAdapter($this, $this->factory->getSoleBackendConfigInstance());
    }

} 