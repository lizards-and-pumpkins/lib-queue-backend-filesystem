<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\Null;

use Brera\Lib\Queue\Backend\Null\NullFactory;
use Brera\Lib\Queue\Tests\Unit\BaseTestCase;

class NullFactoryTest extends BaseTestCase
{
    /**
     * @var NullFactory
     */
    private $backendFactory;
    
    public function setUp()
    {
        $this->backendFactory = new NullFactory();
    }
    
    public function testItReturnsANullBackendConfig()
    {
        $result = $this->backendFactory->getNewBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConfig', $result);
    }
    
    public function testItReturnsANullBackendAdapter()
    {
        $stubBackendConfig = $this->getStubBackendConfig();
        $this->backendFactory->setConfiguredBackendConfigInstance($stubBackendConfig);
        $result = $this->backendFactory->getBackendAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullAdapter', $result);
    }
} 