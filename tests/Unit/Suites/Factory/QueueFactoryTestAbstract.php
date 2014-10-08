<?php

namespace Brera\Lib\Queue\Tests\Unit\Factory;

use Brera\Lib\Queue\Factory\AbstractQueueFactory;

abstract class QueueFactoryTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractQueueFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stubRepository;

    public function setUp()
    {
        $this->stubRepository = $this->getBreraQueueStubDisableOriginalConstructor('Repository');
        $this->factory = $this->getInstance();
        $this->factory->setRepository($this->stubRepository);
    }
    
    /**
     * @return AbstractQueueFactory
     */
    abstract protected function getInstance();

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::__construct
     */
    public function itShouldImplementTheFactoryInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\FactoryInterface', $this->factory);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::setRepository
     */
    public function theRepositoryShouldBeSettable()
    {
        $newStubRepository = $this->getBreraQueueStubDisableOriginalConstructor('Repository');
        $this->factory->setRepository($newStubRepository);
        $this->assertAttributeSame($newStubRepository, 'repository', $this->factory);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getNewRepository
     */
    public function itShouldReturnARepositoryInstance()
    {
        $result = $this->factory->getNewRepository();
        $this->assertInstanceOf('Brera\Lib\Queue\Repository', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getNewRepository
     */
    public function itShouldAlwaysReturnANewRepositoryInstance()
    {
        $result1 = $this->factory->getNewRepository();
        $result2 = $this->factory->getNewRepository();
        $this->assertNotSame($result1, $result2);
    }
    
    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getConsumerQueue
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
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getProducerQueue
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
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getOutgoingMessage
     */
    public function itShouldReturnAnOutgoingMessage()
    {
        $result = $this->factory->getOutgoingMessage('test-channel', 'test-payload', 'test-id');
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessage', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getIncomingMessage
     */
    public function itShouldReturnAnIncomingMessage()
    {
        $result = $this->factory->getIncomingMessage('test-channel', 'test-payload', 'test-id');
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessage', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getMessageBuilder
     */
    public function itShouldReturnAMessageBuilderInstance()
    {
        $result = $this->factory->getMessageBuilder();
        $this->assertInstanceOf('Brera\Lib\Queue\MessageBuilder', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getRegisteredBackendFactoryInstance
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
     * @covers Brera\Lib\Queue\Factory\AbstractQueueFactory::getRegisteredBackendConfigInstance
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
    protected function getBreraQueueStubDisableOriginalConstructor($class)
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