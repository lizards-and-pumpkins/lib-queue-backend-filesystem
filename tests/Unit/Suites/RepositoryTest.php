<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\Repository;

class RepositoryTest extends BaseTestCase
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;
    
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->repository = new Repository($this->stubFactory);
    }
    
    public function testItReturnsAConfig()
    {
        $this->addStubConfigToStubFactory($this->stubFactory);
        $result = $this->repository->getConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\ConfigInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameConfigInstance()
    {
        $this->addStubConfigToStubFactory($this->stubFactory);
        $result1 = $this->repository->getConfig();
        $result2 = $this->repository->getConfig();
        $this->assertSame($result1, $result2);
    }
    
    public function testItReturnsAQueue()
    {
        $this->addStubQueueToStubFactory($this->stubFactory);
        $result = $this->repository->getQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\QueueInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameQueueInstance()
    {
        $this->addStubQueueToStubFactory($this->stubFactory);
        $result1 = $this->repository->getQueue();
        $result2 = $this->repository->getQueue();
        $this->assertSame($result1, $result2);
    }
    
    public function testItReturnsABackendFactory()
    {
        $this->addStubBackendFactoryToStubFactory($this->stubFactory);
        $this->addStubBackendConfigToStubFactory($this->stubFactory);
        $result = $this->repository->getBackendFactory();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendFactoryInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameBackendFactoryInstance()
    {
        $this->addStubBackendFactoryToStubFactory($this->stubFactory);
        $this->addStubBackendConfigToStubFactory($this->stubFactory);
        $result1 = $this->repository->getBackendFactory();
        $result2 = $this->repository->getBackendFactory();
        $this->assertSame($result1, $result2);
    }
    
    public function testItReturnsABackendAdapter()
    {
        $this->addStubBackendAdapterToStubFactory($this->stubFactory);
        $result = $this->repository->getBackendAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendAdapterInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameBackendAdapterInstance()
    {
        $this->addStubBackendAdapterToStubFactory($this->stubFactory);
        $result1 = $this->repository->getBackendAdapter();
        $result2 = $this->repository->getBackendAdapter();
        $this->assertSame($result1, $result2);
    }
    
    public function testItReturnsABackendConfig()
    {
        $this->addStubBackendConfigToStubFactory($this->stubFactory);
        $this->addStubBackendFactoryToStubFactory($this->stubFactory);
        $result = $this->repository->getBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendConfigInterface', $result);
    }
    
    public function testItAlwaysReturnsTheSameBackendConfigInstance()
    {
        $this->addStubBackendConfigToStubFactory($this->stubFactory);
        $this->addStubBackendFactoryToStubFactory($this->stubFactory);
        $result1 = $this->repository->getBackendConfig();
        $result2 = $this->repository->getBackendConfig();
        $this->assertSame($result1, $result2);
    }
    
    public function testItReturnsTheConfiguredBackendClassFromTheConfig()
    {
        $mockConfig = $this->getStubConfig();
        $mockConfig->expects($this->once())
            ->method('getBackendFactoryClass')
            ->will($this->returnValue('test'));
        $this->addStubConfigToStubFactory($this->stubFactory, $mockConfig);
        
        $result = $this->repository->getConfiguredBackendFactoryClass();
        $this->assertEquals('test', $result);
    }
} 