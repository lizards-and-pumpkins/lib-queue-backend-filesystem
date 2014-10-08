<?php


namespace Brera\Lib\Queue\Tests\Unit\Factory;

use Brera\Lib\Queue\Factory\FileQueueFactory;

require_once __DIR__ . '/QueueFactoryTestAbstract.php';

class FileQueueFactoryTest extends QueueFactoryTestAbstract
{
    protected function getInstance()
    {
        return new FileQueueFactory();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Factory\FileQueueFactory::getNewBackendFactory
     */
    public function itShouldReturnABackendFactoryInstance()
    {
        $this->stubRepository->expects($this->never())
            ->method('getConfiguredBackendFactoryClass');
        $result = $this->factory->getNewBackendFactory();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileFactory', $result);
    }
} 