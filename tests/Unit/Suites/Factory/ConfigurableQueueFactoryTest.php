<?php


namespace Brera\Lib\Queue\Tests\Unit\Factory;

use Brera\Lib\Queue\Factory\ConfigurableQueueFactory;

require_once __DIR__ . '/QueueFactoryTestAbstract.php';

class ConfigurableFactoryTest extends QueueFactoryTestAbstract
{
    /**
     * @var ConfigurableQueueFactory
     */
    protected $factory;
    
    protected function getInstance()
    {
        return new ConfigurableQueueFactory();
    }

    /**
     * @test
     */
    public function itShouldReturnTheSetBackendFactoryClass()
    {
        $testClass = 'test-class';
        $this->factory->setBackendFactoryClass($testClass);
        $this->assertEquals($testClass, $this->factory->getBackendFactoryClass());
    }

    /**
     * @test
     */
    public function itShouldReturnADefaultBackendFactoryClassIfNoneIsSet()
    {
        $this->assertNotNull($this->factory->getBackendFactoryClass());
    }

    /**
     * @test
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
} 