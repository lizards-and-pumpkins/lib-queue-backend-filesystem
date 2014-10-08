<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\File,
    Brera\Lib\Queue\Backend\File\Exception\RuntimeException,
    org\bovigo\vfs\vfsStream,
    org\bovigo\vfs\vfsStreamDirectory;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $vfsRoot;

    /**
     * @var File
     */
    private $file;

    private $realFsChannelDir;

    private $realFsPendingStateDir;

    private $realFsProcessingStateDir;

    public function setUp()
    {
        $this->vfsRoot = vfsStream::setup('vfsRoot');
        $this->file = new File();

        /**
         * Unfortunately vfs can't deal with glob and file locking, so some test run on real file system
         */
        $this->realFsChannelDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'brera-lib-queue-file';
        $this->realFsPendingStateDir = $this->realFsChannelDir . DIRECTORY_SEPARATOR . 'pending';
        $this->realFsProcessingStateDir = $this->realFsChannelDir . DIRECTORY_SEPARATOR . 'processing';

        mkdir($this->realFsChannelDir);
        mkdir($this->realFsPendingStateDir);
        mkdir($this->realFsProcessingStateDir);
    }

    public function tearDown()
    {
        @unlink($this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'baz_1');
        @unlink($this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'baz');
        @unlink($this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'foo');
        rmdir($this->realFsProcessingStateDir);
        rmdir($this->realFsPendingStateDir);
        rmdir($this->realFsChannelDir);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::getNewBaseFilename
     */
    public function itShouldReturnNonemptyString()
    {
        $result = $this->file->getNewBaseFilename();
        $this->assertFalse(empty($result));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::getUniqueFilename
     */
    public function itShouldReturnUniqueFileNamesAgainstMultipleDirs()
    {
        touch($this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'foo');
        touch($this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'baz');
        touch($this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'baz_1');

        $globPattern = $this->realFsChannelDir . DIRECTORY_SEPARATOR . '*';

        $result = $this->file->getUniqueFilename($globPattern, 'foo');
        $this->assertEquals('foo_1', $result);

        $result = $this->file->getUniqueFilename($globPattern, 'bar');
        $this->assertEquals('bar', $result);

        $result = $this->file->getUniqueFilename($globPattern, 'baz');
        $this->assertEquals('baz_2', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::moveFile
     */
    public function itShouldMoveAFile()
    {
        $currentDirectory = vfsStream::newDirectory('bar')->at($this->vfsRoot);
        $file = vfsStream::newFile('foo')->at($currentDirectory);
        $newDirectory = vfsStream::newDirectory('baz')->at($this->vfsRoot);

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
    public function itShouldFailToMoveAFileToNonExistingDirectory()
    {
        $currentDirectory = vfsStream::newDirectory('bar')->at($this->vfsRoot);
        $file = vfsStream::newFile('foo')->at($currentDirectory);

        $nonExistingPath = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'baz');
        $this->file->moveFile($file->url(), $nonExistingPath, 'foo');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::writeFile
     */
    public function itShouldWriteToAFile()
    {
        $filePath = $this->realFsProcessingStateDir . DIRECTORY_SEPARATOR . 'foo';
        $payload = 'test-message';

        $this->file->writeFile($filePath, $payload);
        $result = file_get_contents($filePath);

        $this->assertEquals($payload, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::readFile
     */
    public function itShouldReturnAFileContents()
    {
        $data = 'test-message';
        $file = vfsStream::newFile('foo')->at($this->vfsRoot);
        file_put_contents($file->url(), $data);
        $result = $this->file->readFile($file->url());

        $this->assertEquals($data, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::getNewFileHandle
     */
    public function itShouldReturnNewFileHandle()
    {
        $path = vfsStream::url('vfsRoot' . DIRECTORY_SEPARATOR . 'foo');
        $handle = $this->file->getNewFileHandle($path, 'w+');

        $this->assertTrue(is_resource($handle));
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\Filesystem\File::removeFile
     */
    public function itShouldRemoveAFile()
    {
        $file = vfsStream::newFile('foo')->at($this->vfsRoot);
        $this->file->removeFile($file->url());
        $this->assertFalse(file_exists($file->url()));
    }
}
