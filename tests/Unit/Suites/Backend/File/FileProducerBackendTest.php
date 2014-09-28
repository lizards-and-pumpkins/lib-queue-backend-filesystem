<?php

namespace Brera\Lib\Queue\Tests\Unit;

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

        $this->backend = new FileProducerBackend($this->config, $this->directory, $this->file);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileProducerBackend::addMessageToQueue
     */
    public function testItReturnsAMessageIdentifier()
    {
        $this->addStorageDirToStubFactory('/tmp');

        $this->file->expects($this->any())
            ->method('getNewBaseFilename')
            ->will($this->returnValue('foo'));

        $this->file->expects($this->any())
            ->method('getUniqueFilename')
            ->will($this->returnValue('foo'));

        $result = $this->backend->addMessageToQueue('test-channel', 'test-message');
        $this->assertEquals('/tmp/test-channel/pending/foo', $result);
    }
}
