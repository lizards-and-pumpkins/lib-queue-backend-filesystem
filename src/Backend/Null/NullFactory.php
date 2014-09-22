<?php


namespace Brera\Lib\Queue\Backend\Null;


use Brera\Lib\Queue\AbstractBackendFactory;

class NullFactory extends AbstractBackendFactory
{
    protected function getBackendConfigClass()
    {
        return 'Brera\Lib\Queue\Backend\Null\NullConfig';
    }

    protected function getConsumerAdapterClass()
    {
        return 'Brera\Lib\Queue\Backend\Null\NullConsumerAdapter';
    }

    protected function getProducerAdapterClass()
    {
        return 'Brera\Lib\Queue\Backend\Null\NullProducerAdapter';
    }
}