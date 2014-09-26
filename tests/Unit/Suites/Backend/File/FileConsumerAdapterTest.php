<?php

namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/abstracts/AbstractTestFileAdapter.php';

use Brera\Lib\Queue\Backend\File\FileConsumerAdapter;

/**
 * Class FileConsumerAdapterTest
 *
 * @package Brera\Lib\Queue\Tests\Unit
 * @covers Brera\Lib\Queue\Backend\File\FileConsumerAdapter
 */
class FileConsumerAdapterTest extends AbstractTestFileAdapter
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubMessageBuilder;

    /**
     * @var FileConsumerAdapter
     */
    private $adapter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBackendImplementation;

    public function setUp()
    {
        $this->stubMessageBuilder = $this->getStubMessageBuilder();
        $this->stubBackendImplementation = $this->getStubFileConsumerBackend();
        $stubBackendConfig = $this->getStubBackendConfig();
        $this->adapter = new FileConsumerAdapter(
            $this->stubMessageBuilder, $stubBackendConfig, $this->stubBackendImplementation
        );
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerAdapter::setMessageAsProcessed
     */
    public function itShouldDelegateToTheBackendToSetAMessageAsProcessed()
    {
        $stubIncomingMessage = $this->getMockBuilder('Brera\Lib\Queue\IncomingMessageInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stubBackendImplementation->expects($this->any())
            ->method('setMessageAsProcessed');

        $this->adapter->setMessageAsProcessed($stubIncomingMessage);
    }
}
 