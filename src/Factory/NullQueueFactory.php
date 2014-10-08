<?php


namespace Brera\Lib\Queue\Factory;


use Brera\Lib\Queue\Backend\Null\NullFactory;

class NullQueueFactory extends AbstractQueueFactory
{
    public function getNewBackendFactory()
    {
        return new NullFactory($this);
    }
} 