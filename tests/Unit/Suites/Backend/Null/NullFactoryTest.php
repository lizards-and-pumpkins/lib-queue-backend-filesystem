<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\Null;

use Brera\Lib\Queue\Backend\Null\NullFactory;

class NullFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NullFactory
     */
    private $backendFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;
    
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->backendFactory = new NullFactory($this->stubFactory);
        $this->backendFactory->setConfiguredBackendConfigInstance($this->getStubNullConfig());
    }

    /**
     * @test
     * @coversNothing
     */
    public function itShouldExtendTheAbstractBackendFactory()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\AbstractBackendFactory', $this->backendFactory);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\Null\NullFactory::getNewBackendConfig
     */
    public function itShouldReturnANullConfigInstance()
    {
        $result = $this->backendFactory->getNewBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConfig', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\Null\NullFactory::getConsumerAdapter
     */
    public function itShouldReturnANullConsumerAdapter()
    {
        $this->setStubMessageBuilderOnStubFactory();
        $result = $this->backendFactory->getConsumerAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConsumerAdapter', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\Null\NullFactory::getProducerAdapter
     */
    public function itShouldReturnANullProducerAdapter()
    {
        $this->setStubMessageBuilderOnStubFactory();
        $result = $this->backendFactory->getProducerAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullProducerAdapter', $result);
    }

    private function getStubFactory()
    {
        $stubFactory = $this->getMockBuilder('Brera\Lib\Queue\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFactory;
    }

    private function setStubMessageBuilderOnStubFactory()
    {
        $this->stubFactory->expects($this->any())
            ->method('getMessageBuilder')
            ->will($this->returnCallback(function() {
                return $this->getStubMessageBuilder();
            }));
    }

    private function getStubMessageBuilder()
    {
        $stubMessageBuilder = $this->getMockBuilder('Brera\Lib\Queue\MessageBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubMessageBuilder;
    }

    private function getStubNullConfig()
    {
        $stubNullConfig = $this->getMock('Brera\Lib\Queue\Backend\Null\NullConfig');
        return $stubNullConfig;
    }
} 