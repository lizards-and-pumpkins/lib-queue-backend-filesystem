<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    org\bovigo\vfs\vfsStream,
    org\bovigo\vfs\vfsStreamDirectory;

/**
 * Class DirectoryTest
 *
 * @package Brera\Lib\Queue\Tests\Unit\Backend\File
 * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory
 */
class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var Directory
     */
    private $directory;

    public function setUp()
    {
        $this->root = vfsStream::setup('vfsRoot');
        $this->directory = new Directory();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::createDirRecursivelyIfNotExists
     */
    public function testDirIsCreated()
    {
        $path = 'vfsRoot' . DIRECTORY_SEPARATOR . 'foo';
        $this->directory->createDirRecursivelyIfNotExists(vfsStream::url($path));
        $this->assertTrue($this->root->hasChild('foo'));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::createDirRecursivelyIfNotExists
     */
    public function testRecursiveDirsAreCreated()
    {
        $path = 'vfsRoot' . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
        $this->directory->createDirRecursivelyIfNotExists(vfsStream::url($path));
        $this->assertTrue($this->root->hasChild('foo' . DIRECTORY_SEPARATOR . 'bar'));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::createDirRecursivelyIfNotExists
     * @expectedException \Exception
     * @expectedExceptionMessage Path already exists but is not a directory.
     */
    public function testExceptionIsThrownIfPathExistsButItIsNotADir()
    {
        vfsStream::newFile('foo')->at($this->root);
        $path = 'vfsRoot' . DIRECTORY_SEPARATOR . 'foo';
        $this->directory->createDirRecursivelyIfNotExists(vfsStream::url($path));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::getNameOfOldestFileInDir
     */
    public function testItReturnsFirstFileInAlphabeticalOrderInADir()
    {
        vfsStream::newFile('foo')->at($this->root);
        vfsStream::newFile('bar')->at($this->root);
        $result = $this->directory->getNameOfOldestFileInDir(vfsStream::url('vfsRoot'));
        $this->assertEquals('bar', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\Directory::getNameOfOldestFileInDir
     */
    public function testItReturnsAnEmptyStringIfDirIsEmpty()
    {
        $result = $this->directory->getNameOfOldestFileInDir(vfsStream::url('vfsRoot'));
        $this->assertEquals('', $result);
    }
}