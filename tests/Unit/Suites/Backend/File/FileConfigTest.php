<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\FileConfig;

class FileConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileConfig
     */
    private $config;
    
    public function setUp()
    {
        $this->config = new FileConfig();
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::setStorageRootDir
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::getStorageRootDir
     */
    public function itShouldReturnAStorageRootDirSet()
    {
        $path = DIRECTORY_SEPARATOR . 'some' . DIRECTORY_SEPARATOR . 'path';
        $this->config->setStorageRootDir($path);
        $this->assertEquals($path, $this->config->getStorageRootDir());
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::setKeepProcessedMessages
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::getKeepProcessedMessages
     */
    public function itShouldReturnKeepProcessedMessagesFlag()
    {
        $this->config->setKeepProcessedMessages(true);
        $this->assertTrue($this->config->getKeepProcessedMessages());
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::getKeepProcessedMessages
     */
    public function itShouldFalseForKeepingProcessedMessagesByDefault()
    {
        $this->assertFalse($this->config->getKeepProcessedMessages());
    }
}
