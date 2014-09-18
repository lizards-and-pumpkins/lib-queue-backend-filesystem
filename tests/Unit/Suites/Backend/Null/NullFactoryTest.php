<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\Null;

use Brera\Lib\Queue\Backend\Null\NullFactory;
use Brera\Lib\Queue\Tests\Unit\BaseTestCase;

class NullFactoryTest extends BaseTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;
    
    /**
     * @var NullFactory
     */
    private $backendFactory;
    
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->backendFactory = new NullFactory($this->stubFactory);
    }
    
    public function testItReturnsANullBackendConfig()
    {
        $result = $this->backendFactory->getNewBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConfig', $result);
    }
    
    public function testItReturnsANullBackendAdapter()
    {
        $this->addStubSoleBackendConfigToStubFactory($this->stubFactory);
        $result = $this->backendFactory->getBackendAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullAdapter', $result);
    }
    
    protected function addStubSoleBackendConfigToStubFactory(\PHPUnit_Framework_MockObject_MockObject $stubFactory)
    {
        $stubBackendConfig = $this->getStubBackendConfig();
        $stubFactory->expects($this->any())
            ->method('getSoleBackendConfigInstance')
            ->will($this->returnValue($stubBackendConfig));
    }
} 