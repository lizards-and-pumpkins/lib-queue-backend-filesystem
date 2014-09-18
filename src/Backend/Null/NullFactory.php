<?php


namespace Brera\Lib\Queue\Backend\Null;

use Brera\Lib\Queue\BackendFactoryInterface;

class NullFactory implements BackendFactoryInterface
{
    /**
     * @return NullConfig
     */
    public function getBackendConfig()
    {
        return new NullConfig();
    }

    /**
     * @return NullAdapter
     */
    public function getBackendAdapter()
    {
        return new NullAdapter();
    }

} 