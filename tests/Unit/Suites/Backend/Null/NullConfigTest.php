<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\Null;

use Brera\Lib\Queue\Backend\Null\NullConfig;

class NullConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NullConfig
     */
    private $config;
    
    public function setUp()
    {
        $this->config = new NullConfig();
    }
    
    public function testItImplementsTheBackendConfigInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\BackendConfigInterface', $this->config);
    }
} 