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
     * @expectedException RuntimeException
     * @expectedExceptionMessage Root storage path is not set.
     */
    public function testItThrowsAnExceptionIfStorageRootIsNotSet()
    {
        $this->addStorageDirToStubFactory(null);
        $this->backend->checkIfChannelIsInitialized('test-channel');
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
