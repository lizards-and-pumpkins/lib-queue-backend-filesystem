<?php


namespace Brera\Lib\Queue\Tests\Unit;

use Brera\Lib\Queue\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @test
     */
    public function itShouldReturnAConfigInstance()
    {
        $this->setStubReturnValueOnStubObject('getNewConfig', 'getStubConfig');
        $result = $this->repository->getConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\ConfigInterface', $result);
    }

    /**
     * @test
     */
    public function itShouldAlwaysReturnTheSameConfigInstance()
    {
        $this->setStubReturnValueOnStubObject('getNewConfig', 'getStubConfig');
        $result1 = $this->repository->getConfig();
        $result2 = $this->repository->getConfig();
        $this->assertSame($result1, $result2);
    }

    /**
     * @test
     */
    public function itShouldReturnABackendFactory()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $result = $this->repository->getBackendFactory();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendFactoryInterface', $result);
    }

    /**
     * @test
     */
    public function itShouldAlwaysReturnTheSameBackendFactoryInstance()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $result1 = $this->repository->getBackendFactory();
        $result2 = $this->repository->getBackendFactory();
        $this->assertSame($result1, $result2);
    }

    /**
     * @test
     */
    public function itShouldReturnAProducerAdapter()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $this->setStubReturnValueOnStubObject('getProducerAdapter', 'getStubProducerAdapter', $this->repository->getBackendFactory());

        $result = $this->repository->getProducerAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerAdapterInterface', $result);
    }

    /**
     * @test
     */
    public function itShouldAlwaysReturnTheSameProducerAdapter()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $this->setStubReturnValueOnStubObject('getProducerAdapter', 'getStubProducerAdapter', $this->repository->getBackendFactory());
        $result1 = $this->repository->getProducerAdapter();
        $result2 = $this->repository->getProducerAdapter();
        $this->assertSame($result1, $result2);
    }

    /**
     * @test
     */
    public function itShouldReturnAConsumerAdapter()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $this->setStubReturnValueOnStubObject('getConsumerAdapter', 'getStubConsumerAdapter', $this->repository->getBackendFactory());

        $result = $this->repository->getConsumerAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\ConsumerAdapterInterface', $result);
    }

    /**
     * @test
     */
    public function itShouldAlwaysReturnTheSameConsumerAdapter()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $this->setStubReturnValueOnStubObject('getConsumerAdapter', 'getStubConsumerAdapter', $this->repository->getBackendFactory());
        $result1 = $this->repository->getConsumerAdapter();
        $result2 = $this->repository->getConsumerAdapter();
        $this->assertSame($result1, $result2);
    }

    /**
     * @test
     */
    public function itShouldReturnABackendConfigInstance()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $this->setStubReturnValueOnStubObject('getNewBackendConfig', 'getStubBackendConfig', $this->repository->getBackendFactory());
        $result = $this->repository->getBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\BackendConfigInterface', $result);
    }

    /**
     * @test
     */
    public function itShouldAlwaysReturnTheSameBackendConfigInstance()
    {
        $this->setStubReturnValueOnStubObject('getNewBackendFactory', 'getStubBackendFactory');
        $this->setStubReturnValueOnStubObject('getNewBackendConfig', 'getStubBackendConfig', $this->repository->getBackendFactory());
        $result1 = $this->repository->getBackendConfig();
        $result2 = $this->repository->getBackendConfig();
        $this->assertSame($result1, $result2);
    }

    /**
     * @test
     */
    public function itShouldReturnTheBackendFactoryClassNameFromTheConfig()
    {
        $testBackendConfigClass = 'TestBackendConfigClass';
        $this->setStubReturnValueOnStubObject('getNewConfig', 'getStubConfig');
        $this->repository->getConfig()->expects($this->any())
            ->method('getBackendFactoryClass')
            ->will($this->returnValue($testBackendConfigClass));
        
        $this->assertEquals($testBackendConfigClass, $this->repository->getConfiguredBackendFactoryClass());
    }

    private function setStubReturnValueOnStubObject($method, $callback, $stubObject = null)
    {
        $stubGetter = function () use ($callback) {
            return $this->$callback();
        };
        $stubTarget = $stubObject ?: $this->stubFactory;
        $stubTarget->expects($this->any())
            ->method($method)
            ->will($this->returnCallback($stubGetter));
    }

    private function getStubFactory()
    {
        $stubFactory = $this->getMockBuilder('Brera\Lib\Queue\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFactory;
    }

    private function getStubConfig()
    {
        $stubConfig = $this->getMockBuilder('Brera\Lib\Queue\Config')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubConfig;
    }

    private function getStubBackendFactory()
    {
        $stubBackendFactory = $this->getMockBuilder('Brera\Lib\Queue\BackendFactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubBackendFactory;
    }

    private function getStubProducerAdapter()
    {
        $stubProducerAdapter = $this->getMockBuilder('Brera\Lib\Queue\ProducerAdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubProducerAdapter;
    }

    private function getStubConsumerAdapter()
    {
        $stubConsumerAdapter = $this->getMockBuilder('Brera\Lib\Queue\ConsumerAdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubConsumerAdapter;
    }

    private function getStubBackendConfig()
    {
        $stubBackendConfig = $this->getMockBuilder('Brera\Lib\Queue\BackendConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubBackendConfig;
    }
} 