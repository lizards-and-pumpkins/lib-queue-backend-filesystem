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

    /**
     * @test
     * @covers Brera\Lib\Queue\Config::setBackendFactoryClass
     * @covers Brera\Lib\Queue\Config::getBackendFactoryClass
     */
    public function theBackendFactoryClassShouldBeSettable()
    {
        $this->config->setBackendFactoryClass('test');
        $this->assertEquals('test', $this->config->getBackendFactoryClass());
    }
} 