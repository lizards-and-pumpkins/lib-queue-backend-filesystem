<?php


namespace Brera\Lib\Queue\Tests\Unit;

use Brera\Lib\Queue\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;
    
    public function setUp()
    {
        $this->config = new Config();
    }
    
    public function testItReturnsTheNullFactoryClassByDefault()
    {
        $this->assertEquals('Brera\Lib\Queue\Backend\Null\NullFactory', $this->config->getBackendFactoryClass());
    }
    
    public function testTheBackendFactoryClassCanBeSet()
    {
        $this->config->setBackendFactoryClass('test');
        $this->assertEquals('test', $this->config->getBackendFactoryClass());
    }
} 