<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\Null;

use Brera\Lib\Queue\Backend\Null\NullFactory;

class NullFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NullFactory
     */
    private $factory;
    
    public function setUp()
    {
        $this->factory = new NullFactory();
    }
    
    public function testItReturnsANullBackendConfig()
    {
        $result = $this->factory->getBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConfig', $result);
    }
    
    public function testItReturnsANullBackendAdapter()
    {
        $result = $this->factory->getBackendAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullAdapter', $result);
    }
} 