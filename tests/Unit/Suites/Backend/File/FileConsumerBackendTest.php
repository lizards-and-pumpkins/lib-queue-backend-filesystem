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

    public function setUp()
    {
        parent::setUp();

        $this->backend = new FileConsumerBackend($this->config, $this->directory, $this->file);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::getNextMessageIdentifier
     */
    public function itShouldDelegateToTheFilesystemFileToGetAnOldestFileInQueue()
    {
        $this->directory->expects($this->any())
            ->method('getNameOfOldestFileInDir')
            ->will($this->returnValue('foo'));

        $this->addMoveFileToFilesystemFile('/test-channel/processing/foo');
        $result = $this->backend->getNextMessageIdentifier('test-channel');

        $this->assertEquals('/test-channel/processing/foo', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConsumerBackend::readMessage
     */
    public function itShouldDelegateToTheFilesystemFileToReadFile()
    {
        $this->file->expects($this->any())
            ->method('readFile')
            ->will($this->returnValue('test-message'));

        $result = $this->backend->readMessage('/dev/null/test-channel/processing/foo');
        $this->assertEquals('test-message', $result);
    }
}
