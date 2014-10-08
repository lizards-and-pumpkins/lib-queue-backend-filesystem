<?php


namespace Brera\Lib\Queue\Tests\Unit\Factory;

use Brera\Lib\Queue\Factory\NullQueueFactory;

require_once __DIR__ . '/QueueFactoryTestAbstract.php';

class NullQueueFactoryTest extends QueueFactoryTestAbstract
{
    protected function getInstance()
    {
        return new NullQueueFactory();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\NullQueueFactory::getNewBackendFactory
     */
    public function itShouldReturnABackendFactoryInstance()
    {
        $this->stubRepository->expects($this->never())
            ->method('getConfiguredBackendFactoryClass');
        $result = $this->factory->getNewBackendFactory();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullFactory', $result);
    }
} 