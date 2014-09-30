<?php

namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/AbstractTestBase.php';

use Brera\Lib\Queue\Backend\File\FileAbstractBackend,
    Brera\Lib\Queue\Backend\File\Exception\RuntimeException;

/**
 * Class AbstractTestFileBackend
 *
 * @package Brera\Lib\Queue\Tests\Unit
 * @covers Brera\Lib\Queue\Backend\File\FileAbstractBackend
 */
class AbstractTestFileBackend extends AbstractTestBase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $file;

    /**
     * @var FileAbstractBackend
     */
    protected $backend;

    public function setUp()
    {
        $this->config = $this->getStubFileConfig();
        $this->directory = $this->getStubFilesystemDirectory();
        $this->file = $this->getStubFilesystemFile();
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

    protected function addMoveFileToFilesystemFile($newPath)
    {
        $this->file->expects($this->any())
            ->method('moveFile')
            ->will($this->returnValue($newPath));
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
        $this->config->expects($this->any())
            ->method('getStorageRootDir')
            ->will($this->returnValue($dirPath));
    }
}
