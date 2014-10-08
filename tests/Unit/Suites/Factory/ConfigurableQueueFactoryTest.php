<?php


namespace Brera\Lib\Queue\Tests\Unit\Factory;

use Brera\Lib\Queue\Factory\ConfigurableQueueFactory;

require_once __DIR__ . '/QueueFactoryTestAbstract.php';

class ConfigurableFactoryTest extends QueueFactoryTestAbstract
{
    protected function getInstance()
    {
        return new ConfigurableQueueFactory();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\ConfigurableQueueFactory::getNewBackendFactory
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