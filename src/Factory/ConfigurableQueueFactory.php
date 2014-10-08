<?php


namespace Brera\Lib\Queue\Factory;


use Brera\Lib\Queue\BackendFactoryInterface;

class ConfigurableQueueFactory extends AbstractQueueFactory
{
    private $defaultBackendFactoryClass = 'Brera\\Lib\\Queue\\Backend\\Null\\NullFactory';

    /**
     * @var string
     */
    private $backendFactoryClass;

    /**
     * @param string $backendFactoryClass
     */
    public function setBackendFactoryClass($backendFactoryClass)
    {
        $this->backendFactoryClass = $backendFactoryClass;
    }
    
    public function getBackendFactoryClass()
    {
        if (is_null($this->backendFactoryClass)) {
            $this->setBackendFactoryClass($this->defaultBackendFactoryClass);
        }
        return $this->backendFactoryClass;
    }
    
    /**
     * @return BackendFactoryInterface
     */
    public function getNewBackendFactory()
    {
        $class = $this->getBackendFactoryClass();
        /** @var BackendFactoryInterface $factory */
        $factory = new $class($this);
        return $factory;
    }
} 