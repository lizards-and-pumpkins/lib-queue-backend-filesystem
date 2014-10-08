<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

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
    protected $stubMessageBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->stubMessageBuilder = $this->getStubMessageBuilder();
        $this->backend = new FileConsumerBackend(
            $this->stubConfig, $this->stubMessageBuilder, $this->stubDirectory, $this->stubFile
        );
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::getMessageFromQueue
     */
    public function itShouldGetAnIncomingMessageFromAQueue()
    {
        $rootDir = DIRECTORY_SEPARATOR . 'tmp';
        $channelName = 'test-channel';
        $fileName = 'foo';

        $this->addStorageDirToStubFactory($rootDir);

        $this->stubDirectory->expects($this->any())
            ->method('getNameOfFirstFileInSortedDir')
            ->will($this->returnValue($fileName));

        $messageIdentifier = $this->getMessageIdentifier($rootDir, $channelName, 'processing', $fileName);
        $message = $this->getStubMessage($channelName, 'test-message', $messageIdentifier);

        $this->stubMessageBuilder->expects($this->any())
            ->method('getIncomingMessage')
            ->will($this->returnValue($message));

        $result = $this->backend->getMessageFromQueue($channelName);

        $this->assertSame($message, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::setMessageAsProcessed
     */
    public function itShouldMoveMessageIntoProcessedState()
    {
        $rootDir = DIRECTORY_SEPARATOR . 'tmp';
        $channelName = 'test-channel';
        $fileName = 'foo';

        $processingMessageIdentifier = $this->getMessageIdentifier($rootDir, $channelName, 'processing', $fileName);
        $completeMessageIdentifier = $this->getMessageIdentifier($rootDir, $channelName, 'complete', $fileName);

        $this->addStorageDirToStubFactory($rootDir);
        $this->addGetKeepProcessedMessagesToStubFactory(true);

        $this->stubFile->expects($this->once())
            ->method('moveFile')
            ->will($this->returnValue($completeMessageIdentifier));

        $message = $this->getStubMessage($channelName, 'test-message', $processingMessageIdentifier);
        $this->backend->setMessageAsProcessed($message);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::setMessageAsProcessed
     */
    public function itShouldRemoveProcessedMessage()
    {
        $rootDir = DIRECTORY_SEPARATOR . 'tmp';
        $channelName = 'test-channel';
        $fileName = 'foo';

        $processingMessageIdentifier = $this->getMessageIdentifier($rootDir, $channelName, 'processing', $fileName);

        $this->addStorageDirToStubFactory($rootDir);
        $this->addGetKeepProcessedMessagesToStubFactory(false);

        $this->stubFile->expects($this->once())
            ->method('removeFile');

        $message = $this->getStubMessage($channelName, 'test-message', $processingMessageIdentifier);
        $this->backend->setMessageAsProcessed($message);
    }
}
