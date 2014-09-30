<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\File,
    Brera\Lib\Queue\Backend\File\Exception\RuntimeException,
    org\bovigo\vfs\vfsStream,
    org\bovigo\vfs\vfsStreamDirectory;

/**
 * Class FileTest
 *
 * @package Brera\Lib\Queue\Tests\Unit\Backend\File
 * @covers Brera\Lib\Queue\Backend\File\Filesystem\File
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var File
     */
    private $file;

    public function setUp()
    {
        $this->root = vfsStream::setup('vfsRoot');
        $this->file = new File();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::getNewBaseFilename
     */
    public function testIfFilenameIsFloat()
    {
        $result = $this->file->getNewBaseFilename();
        $this->assertTrue(is_float($result));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::getUniqueFilename
     */
    public function testItChecksTheFileAgainstMultipleDirsAndAddsIncrementAccordingly()
    {
        /* Test is not possible as glob can not work with vfs */
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::moveFile
     */
    public function testItMovesAFile()
    {
        $currentDirectory = vfsStream::newDirectory('bar')->at($this->root);
        $file = vfsStream::newFile('foo')->at($currentDirectory);
        $newDirectory = vfsStream::newDirectory('baz')->at($this->root);

        $this->file->moveFile($file->url(), $newDirectory->url(), 'foo');

        $this->assertTrue(file_exists($newDirectory->url() . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertFalse(file_exists($currentDirectory->url() . DIRECTORY_SEPARATOR . 'foo'));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::moveFile
     * @expectedException RuntimeException
     * @expectedExceptionMessage Can not move the file.
     */
    public function testItFailsToMoveAFile()
    {
        $currentDirectory = vfsStream::newDirectory('bar')->at($this->root);
        $file = vfsStream::newFile('foo')->at($currentDirectory);

        $nonExistingPath = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'baz');
        $this->file->moveFile($file->url(), $nonExistingPath, 'foo');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::writeFile
     */
    public function testItWritesToAFile()
    {
        /* Can't test it as exclusive lock can't be set on vfsStream file */
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::readFile
     */
    public function testItReturnsAFileContents()
    {
        $data = 'test-message';
        $file = vfsStream::newFile('foo')->at($this->root);
        file_put_contents($file->url(), $data);
        $result = $this->file->readFile($file->url());

        $this->assertEquals($data, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::getNewFileHandle
     */
    public function testItReturnsNewFileHandle()
    {
        $path = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'foo');
        $handle = $this->file->getNewFileHandle($path, 'w+');

        $this->assertTrue(is_resource($handle));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::removeFile
     */
    public function testFileIsRemoved()
    {
        $file = vfsStream::newFile('foo')->at($this->root);
        $this->file->removeFile($file->url());
        $this->assertFalse(file_exists($file->url()));
    }
}
