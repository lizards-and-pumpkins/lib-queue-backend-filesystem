<?php


namespace Brera\Lib\Queue;


class Config implements ConfigInterface
{
    private $backendFactoryClass = 'Brera\Lib\Queue\Backend\Null\NullFactory';
    
    /**
     * @return string
     */
    public function getBackendFactoryClass()
    {
        return $this->backendFactoryClass;
    }

    /**
     * @param string $backendFactoryClassName
     */
    public function setBackendFactoryClass($backendFactoryClassName)
    {
        $this->backendFactoryClass = $backendFactoryClassName;
    }
} 