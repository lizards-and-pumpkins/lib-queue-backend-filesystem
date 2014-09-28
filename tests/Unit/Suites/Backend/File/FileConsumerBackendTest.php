<?php

namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/abstracts/AbstractTestFileBackend.php';

use Brera\Lib\Queue\Backend\File\FileConsumerBackend;

/**
 * Class FileConsumerBackendTest
 *
 * @package Brera\Lib\Queue\Tests\Unit
 * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend
 */
class FileConsumerBackendTest extends AbstractTestFileBackend
{
    /**
     * @var FileConsumerBackend
     */
    protected $backend;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->messageBuilder = $this->getStubMessageBuilder();
        $this->backend = new FileConsumerBackend($this->config, $this->messageBuilder, $this->directory, $this->file);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::getMessageFromQueue
     */
    public function testItGetsAnIncomingMessage()
    {
        $this->addStorageDirToStubFactory('/tmp');

        $this->directory->expects($this->any())
            ->method('getNameOfOldestFileInDir')
            ->will($this->returnValue('foo'));

        $message = $this->getStubMessage('test-channel', 'test-message', '/tmp/test-channel/processing/foo');

        $this->messageBuilder->expects($this->any())
            ->method('getIncomingMessage')
            ->will($this->returnValue($message));

        $result = $this->backend->getMessageFromQueue('test-channel');

        $this->assertEquals('test-channel', $result->getChannel());
        $this->assertEquals('test-message', $result->getPayload());
        $this->assertEquals('/tmp/test-channel/processing/foo', $result->getIdentifier());
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::setMessageAsProcessed
     */
    public function testItMovesMessageIntoProcessedState()
    {
        $this->addStorageDirToStubFactory('/tmp');
        $this->addMoveFileToFilesystemFile('/tmp/test-channel/complete/foo');
        $message = $this->getStubMessage('test-channel', 'test-message', '/tmp/test-channel/processing/foo');
        $this->backend->setMessageAsProcessed($message);
    }
}
