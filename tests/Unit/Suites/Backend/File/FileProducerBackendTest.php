<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

require_once __DIR__ . '/abstracts/AbstractTestFileBackend.php';

use Brera\Lib\Queue\Backend\File\FileProducerBackend;

/**
 * Class FileProducerBackendTest
 *
 * @package Brera\Lib\Queue\Tests\Unit
 * @covers Brera\Lib\Queue\Backend\File\FileProducerBackend
 */
class FileProducerBackendTest extends AbstractTestFileBackend
{
    /**
     * @var FileProducerBackend
     */
    protected $backend;

    public function setUp()
    {
        parent::setUp();

        $this->backend = new FileProducerBackend($this->stubConfig, $this->stubDirectory, $this->stubFile);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileProducerBackend::addMessageToQueue
     */
    public function itShouldReturnTheFilePathOfAMessage()
    {
        $rootDir = DIRECTORY_SEPARATOR . 'tmp';
        $channelName = 'test-channel';
        $fileName = 'foo';

        $messageIdentifier = $this->getMessageIdentifier($rootDir, $channelName, 'pending', $fileName);

        $this->addStorageDirToStubFactory($rootDir);

        $this->stubFile->expects($this->any())
            ->method('getNewBaseFilename')
            ->will($this->returnValue($fileName));

        $this->stubFile->expects($this->any())
            ->method('getUniqueFilename')
            ->will($this->returnValue($fileName));

        $result = $this->backend->addMessageToQueue($channelName, 'test-message');
        $this->assertEquals($messageIdentifier, $result);
    }
}
