<?php

namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\Factory;

class FactoryTest extends BaseTestCase
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
        $stubRepository = $this->getStubRepository();
        $this->stubRepository = $stubRepository;
        $this->factory = new Factory();
        $this->factory->setRepository($stubRepository);
    }

    public function testItReturnsARepository()
    {
        $result = $this->factory->getNewRepository();
        $this->assertInstanceOf('Brera\Lib\Queue\RepositoryInterface', $result);
    }

    public function testItReturnsANewRepositoryOnEachCall()
    {
        $result1 = $this->factory->getNewRepository();
        $result2 = $this->factory->getNewRepository();
        $this->assertNotSame($result1, $result2);
    }
    
    public function testItReturnsANewRepositoryEvenAfterOneWasSet()
    {
        $repository1 = $this->factory->getNewRepository();
        $this->factory->setRepository($repository1);
        $repository2 = $this->factory->getNewRepository();
        $this->assertNotSame($repository1, $repository2);
    }
    
    public function testItReturnsAConfigInstance()
    {
        $result = $this->factory->getNewConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\ConfigInterface', $result);
    }
    
    public function testItAlwaysReturnsANewConfigInstance()
    {
        $result1 = $this->factory->getNewConfig();
        $result2 = $this->factory->getNewConfig();
        $this->assertNotSame($result1, $result2);
    }

    public function testItReturnsAConfigInstanceFromRepository()
    {
        $this->addStubConfigToStubRepository($this->stubRepository);
        $result = $this->factory->getSoleConfigInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\ConfigInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameConfigInstanceFromRepository()
    {
        $this->addStubConfigToStubRepository($this->stubRepository);
        $result1 = $this->factory->getSoleConfigInstance();
        $result2 = $this->factory->getSoleConfigInstance();
        $this->assertSame($result1, $result2);
    }

    public function testItReturnsAQueue()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result = $this->factory->getQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\QueueInterface', $result);
    }
    
    public function testItReturnsAProducerChannel()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result = $this->factory->getProducerChannel('test-name');
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerChannelInterface', $result);
    }
    
    public function testItReturnsAConsumerChannel()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result = $this->factory->getConsumerChannel('test-name');
        $this->assertInstanceOf('Brera\Lib\Queue\ConsumerChannelInterface', $result);
    }
    
    public function testItReturnsAnOutgoingMessage()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result = $this->factory->getOutgoingMessage($this->getStubProducerChannel(), 'test-payload');
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessageInterface', $result);
    }
    
    public function testItReturnsAnIncomingMessage()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result = $this->factory->getIncomingMessage($this->getStubConsumerChannel(), 'test-payload');
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessageInterface', $result);
    }
    
    public function testItReturnsABackendFactory()
    {
        $this->addBackendFactoryClassToStubRepository($this->stubRepository);
        $result = $this->factory->getBackendFactory();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendFactoryInterface', $result);
    }
    
    public function testItReturnsABackendConfig()
    {
        $this->addBackendFactoryClassToStubRepository($this->stubRepository);
        $result = $this->factory->getNewBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendConfigInterface', $result);
    }
    
    public function testItReturnsANewBackendConfigInstance()
    {
        $this->addBackendFactoryClassToStubRepository($this->stubRepository);
        $result1 = $this->factory->getNewBackendConfig();
        $result2 = $this->factory->getNewBackendConfig();
        $this->assertNotSame($result1, $result2);
    }
    
    public function testItReturnsABackendConfigFromRepository()
    {
        $this->addStubBackendConfigToStubRepository($this->stubRepository);
        $result = $this->factory->getSoleBackendConfigInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendConfigInterface', $result);
    }

    public function testItReturnsTheConfigInstanceFromTheRepository()
    {
        $this->addStubBackendConfigToStubRepository($this->stubRepository);
        $this->stubRepository->expects($this->once())
            ->method('getBackendConfig');
        $this->factory->getSoleBackendConfigInstance();
    }
    
    public function testItReturnsABackendAdapter()
    {
        $this->addBackendFactoryClassToStubRepository($this->stubRepository);
        $result = $this->factory->getNewBackendAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendAdapterInterface', $result);
    }
    
    public function testItAlwaysReturnsANewBackendAdapter()
    {
        $this->addBackendFactoryClassToStubRepository($this->stubRepository);
        $result1 = $this->factory->getNewBackendAdapter();
        $result2 = $this->factory->getNewBackendAdapter();
        $this->assertNotSame($result1, $result2);
    }
    
    public function testItReturnsABackendAdapterFromTheRepository()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result = $this->factory->getSoleBackendAdapterInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendAdapterInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameBackendAdapterFromTheRepository()
    {
        $this->addStubBackendAdapterToStubRepository($this->stubRepository);
        $result1 = $this->factory->getSoleBackendAdapterInstance();
        $result2 = $this->factory->getSoleBackendAdapterInstance();
        $this->assertSame($result1, $result2);
    }
} 