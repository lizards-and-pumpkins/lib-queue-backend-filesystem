<?php


namespace Brera\Lib\Queue\Tests\Unit\Backend\Null;

use Brera\Lib\Queue\Backend\Null\NullProducerAdapter;

class NullProducerAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NullProducerAdapter
     */
    private $adapter;
    
    public function setUp()
    {
        $stubMessageBuilder = $this->getStubMessageBuilder();
        $stubBackendConfig = $this->getStubBackendConfig();
        $this->adapter = new NullProducerAdapter($stubMessageBuilder, $stubBackendConfig);
    }

    /**
     * @test
     */
    public function itShouldImplementTheProducerAdapterInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerAdapterInterface', $this->adapter);
    }

    private function getStubMessageBuilder()
    {
        $stubMessageBuilder = $this->getMockBuilder('Brera\Lib\Queue\MessageBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubMessageBuilder;
    }

    private function getStubBackendConfig()
    {
        $stubNullConfig = $this->getMockBuilder('Brera\Lib\Queue\Backend\Null\NullConfig')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubNullConfig;
    }
} 