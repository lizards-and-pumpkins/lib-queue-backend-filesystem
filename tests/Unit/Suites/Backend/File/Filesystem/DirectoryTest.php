<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    Brera\Lib\Queue\Backend\File\Exception\RuntimeException,
    org\bovigo\vfs\vfsStream,
    org\bovigo\vfs\vfsStreamDirectory;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $vfsRoot;

    /**
     * @var Directory
     */
    private $directory;

    public function setUp()
    {
        $this->vfsRoot = vfsStream::setup('vfsRoot');
        $this->directory = new Directory();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::createDirRecursivelyIfNotExists
     */
    public function itShouldCreateADirectory()
    {
        $path = 'vfsRoot' . DIRECTORY_SEPARATOR . 'foo';
        $this->directory->createDirRecursivelyIfNotExists(vfsStream::url($path));
        $this->assertTrue($this->vfsRoot->hasChild('foo'));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::createDirRecursivelyIfNotExists
     */
    public function itShouldCreateRecursiveDirectories()
    {
        $path = 'vfsRoot' . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
        $this->directory->createDirRecursivelyIfNotExists(vfsStream::url($path));
        $this->assertTrue($this->vfsRoot->hasChild('foo' . DIRECTORY_SEPARATOR . 'bar'));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::createDirRecursivelyIfNotExists
     * @expectedException RuntimeException
     * @expectedExceptionMessage Path already exists but is not a directory.
     */
    public function itShouldFailToCreateADirIfFileAlreadyExistsAtThisPath()
    {
        $file = vfsStream::newFile('foo')->at($this->vfsRoot);
        $this->directory->createDirRecursivelyIfNotExists($file->url());
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::getNameOfFirstFileInSortedDir
     */
    public function itShouldReturnFirstFileInAlphabeticalOrderInADir()
    {
        vfsStream::newFile('foo')->at($this->vfsRoot);
        vfsStream::newFile('bar')->at($this->vfsRoot);
        $result = $this->directory->getNameOfFirstFileInSortedDir($this->vfsRoot->url());
        $this->assertEquals('bar', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::getNameOfFirstFileInSortedDir
     */
    public function itShouldReturnAnEmptyStringIfDirIsEmpty()
    {
        $result = $this->directory->getNameOfFirstFileInSortedDir($this->vfsRoot->url());
        $this->assertEquals('', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::getNameOfFirstFileInSortedDir
     */
    public function itShouldReturnAFile()
    {
        vfsStream::newFile('foo')->at($this->vfsRoot);
        vfsStream::newDirectory('bar')->at($this->vfsRoot);
        $result = $this->directory->getNameOfFirstFileInSortedDir($this->vfsRoot->url());
        $this->assertEquals('foo', $result);
    }
}
