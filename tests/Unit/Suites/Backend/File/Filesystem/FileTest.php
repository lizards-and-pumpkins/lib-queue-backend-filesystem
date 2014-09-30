<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\File,
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
        $dirStream = vfsStream::newDirectory('bar')->at($this->root);
        vfsStream::newFile('foo')->at($dirStream);
        vfsStream::newDirectory('baz')->at($this->root);

        $currentPath = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'foo');
        $newPath = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'baz');
        $this->file->moveFile($currentPath, $newPath, 'foo');

        $this->assertTrue(file_exists($newPath . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertFalse(file_exists($currentPath));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::moveFile
     * @expectedException \Exception
     * @expectedExceptionMessage Can not move the file.
     */
    public function testItFailsToMoveAFile()
    {
        $dirStream = vfsStream::newDirectory('bar')->at($this->root);
        vfsStream::newFile('foo')->at($dirStream);

        $currentPath = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'foo');
        $newPath = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'baz');
        $this->file->moveFile($currentPath, $newPath, 'foo');
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
        $fileStream = vfsStream::newFile('foo')->at($this->root);
        file_put_contents($fileStream->url(), $data);
        $result = $this->file->readFile($fileStream->url());

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
}
