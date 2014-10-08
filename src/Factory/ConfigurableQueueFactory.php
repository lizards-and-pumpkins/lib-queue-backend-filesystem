<?php


namespace Brera\Lib\Queue\Factory;


use Brera\Lib\Queue\BackendFactoryInterface;

class ConfigurableQueueFactory extends AbstractQueueFactory
{
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
} 