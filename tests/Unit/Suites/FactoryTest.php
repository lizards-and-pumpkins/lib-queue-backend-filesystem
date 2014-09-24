<?php

namespace Brera\Lib\Queue\Tests\Unit;

use Brera\Lib\Queue\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubRepository;

    public function setUp()
    {
        $this->stubRepository = $this->getBreraQueueStubDisableOriginalConstructor('Repository');
        $this->factory = new Factory();
        $this->factory->setRepository($this->stubRepository);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::__construct
     */
    public function itShouldImplementTheFactoryInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\FactoryInterface', $this->factory);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::setRepository
     */
    public function theRepositoryShouldBeSettable()
    {
        $newStubRepository = $this->getBreraQueueStubDisableOriginalConstructor('Repository');
        $this->factory->setRepository($newStubRepository);
        $this->assertAttributeSame($newStubRepository, 'repository', $this->factory);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getNewRepository
     */
    public function itShouldReturnARepositoryInstance()
    {
        $result = $this->factory->getNewRepository();
        $this->assertInstanceOf('Brera\Lib\Queue\Repository', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getNewRepository
     */
    public function itShouldAlwaysReturnANewRepositoryInstance()
    {
        $result1 = $this->factory->getNewRepository();
        $result2 = $this->factory->getNewRepository();
        $this->assertNotSame($result1, $result2);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getRegisteredConfigInstance
     */
    public function itShouldReturnTheConfigInstanceFromTheRepository()
    {
        $stubConfig = $this->getBreraQueueStubDisableOriginalConstructor('Config');
        $this->setStubReturnValueOnStubRepository('getConfig', $stubConfig);
        
        $result = $this->factory->getRegisteredConfigInstance();
        $this->assertSame($stubConfig, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getNewConfig
     */
    public function itShouldReturnAConfigInstance()
    {
        $result = $this->factory->getNewConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\Config', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getConsumerQueue
     */
    public function itShouldReturnAConsumerQueue()
    {
        $stubConsumerAdapter = $this->getBreraQueueStubDisableOriginalConstructor('ConsumerAdapterInterface');
        $this->setStubReturnValueOnStubRepository('getConsumerAdapter', $stubConsumerAdapter);
        $result = $this->factory->getConsumerQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\ConsumerQueue', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getProducerQueue
     */
    public function itShouldReturnAProducerQueue()
    {
        $stubProducerAdapter = $this->getBreraQueueStubDisableOriginalConstructor('ProducerAdapterInterface');
        $this->setStubReturnValueOnStubRepository('getProducerAdapter', $stubProducerAdapter);
        $result = $this->factory->getProducerQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerQueue', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getOutgoingMessage
     */
    public function itShouldReturnAnOutgoingMessage()
    {
        $result = $this->factory->getOutgoingMessage('test-channel', 'test-payload', 'test-id');
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessage', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getIncomingMessage
     */
    public function itShouldReturnAnIncomingMessage()
    {
        $result = $this->factory->getIncomingMessage('test-channel', 'test-payload', 'test-id');
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessage', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getMessageBuilder
     */
    public function itShouldReturnAMessageBuilderInstance()
    {
        $result = $this->factory->getMessageBuilder();
        $this->assertInstanceOf('Brera\Lib\Queue\MessageBuilder', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getNewBackendFactory
     */
    public function itShouldReturnABackendFactoryInstance()
    {
        $stubBackendFactory = $this->getBreraQueueStubDisableOriginalConstructor('BackendFactoryInterface');
        $this->stubRepository->expects($this->any())
            ->method('getConfiguredBackendFactoryClass')
            ->will($this->returnValue(get_class($stubBackendFactory)));
        $result = $this->factory->getNewBackendFactory();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendFactoryInterface', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getRegisteredBackendFactoryInstance
     */
    public function itShouldReturnTheBackendFactoryFromTheRepository()
    {
        $stubBackendFactory = $this->getBreraQueueStubDisableOriginalConstructor('ProducerAdapterInterface');
        $this->setStubReturnValueOnStubRepository('getBackendFactory', $stubBackendFactory);
        $result = $this->factory->getRegisteredBackendFactoryInstance();
        $this->assertSame($stubBackendFactory, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory::getRegisteredBackendConfigInstance
     */
    public function itShouldReturnTheRegisteredBackendConfigInstance()
    {
        $stubBackendConfig = $this->getBreraQueueStubDisableOriginalConstructor('BackendConfigInterface');
        $this->setStubReturnValueOnStubRepository('getBackendConfig', $stubBackendConfig);
        $result = $this->factory->getRegisteredBackendConfigInstance();
        $this->assertSame($stubBackendConfig, $result);
    }

    /**
     * @param string $class
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getBreraQueueStubDisableOriginalConstructor($class)
    {
        $stubObject = $this->getMockBuilder('Brera\Lib\Queue\\' . ucfirst($class))
            ->disableOriginalConstructor()
            ->getMock();
        return $stubObject;
    }

    private function setStubReturnValueOnStubRepository($methodName, $stubObject)
    {
        $this->stubRepository->expects($this->any())
            ->method($methodName)
            ->will($this->returnValue($stubObject));
    }
} 