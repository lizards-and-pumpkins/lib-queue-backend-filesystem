<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

require_once __DIR__ . '/AbstractTestBase.php';

use Brera\Lib\Queue\Backend\File\FileAbstractBackend,
    Brera\Lib\Queue\Backend\File\Exception\RuntimeException;

class AbstractTestFileBackend extends AbstractTestBase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stubConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stubDirectory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stubFile;

    /**
     * @var FileAbstractBackend
     */
    protected $backend;

    public function setUp()
    {
        $this->stubConfig = $this->getStubFileConfig();
        $this->stubDirectory = $this->getStubFilesystemDirectory();
        $this->stubFile = $this->getStubFilesystemFile();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileAbstractBackend::checkIfChannelIsInitialized
     */
    public function itShouldPassTheChannelInitializationWithoutMessagesBeingKept()
    {
        $this->addStorageDirToStubFactory('/some/path');

        $this->addGetKeepProcessedMessagesToStubFactory(false);
        $this->stubDirectory->expects($this->exactly(2))
            ->method('createDirRecursivelyIfNotExists');

        $this->backend->checkIfChannelIsInitialized('test-channel');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileAbstractBackend::checkIfChannelIsInitialized
     */
    public function itShouldPassTheChannelInitializationWithMessagesBeingKept()
    {
        $this->addStorageDirToStubFactory('/some/path');

        $this->addGetKeepProcessedMessagesToStubFactory(true);
        $this->stubDirectory->expects($this->exactly(3))
            ->method('createDirRecursivelyIfNotExists');

        $this->backend->checkIfChannelIsInitialized('test-channel');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileAbstractBackend::checkIfChannelIsInitialized
     * @expectedException RuntimeException
     * @expectedExceptionMessage Root storage path is not set.
     */
    public function itShouldThrowAnExceptionIfStorageRootIsNotSet()
    {
        $this->backend->checkIfChannelIsInitialized('test-channel');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileAbstractBackend::changeMessageState
     */
    public function itShouldMoveFileToAnotherStateDir()
    {
        $this->addStorageDirToStubFactory('/some/path');
        $this->stubFile->expects($this->once())
            ->method('moveFile');

        $this->backend->changeMessageState('test-channel', '/some/file/path', 'new-state');
    }

    private function getStubFileConfig()
    {
        $stubConfig = $this->getMockBuilder('Brera\Lib\Queue\Backend\File\FileConfig')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubConfig;
    }

    private function getStubFilesystemDirectory()
    {
        $stubFilesystemDirectory = $this->getMockBuilder('Brera\Lib\Queue\Backend\File\Filesystem\Directory')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFilesystemDirectory;
    }

    private function getStubFilesystemFile()
    {
        $stubFilesystemFile = $this->getMockBuilder('Brera\Lib\Queue\Backend\File\Filesystem\File')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFilesystemFile;
    }

    protected function addStorageDirToStubFactory($dirPath)
    {
        $this->stubConfig->expects($this->atLeastOnce())
            ->method('getStorageRootDir')
            ->will($this->returnValue($dirPath));
    }

    protected function addGetKeepProcessedMessagesToStubFactory($returnVal)
    {
        $this->stubConfig->expects($this->atLeastOnce())
            ->method('getKeepProcessedMessages')
            ->will($this->returnValue($returnVal));
    }
}
