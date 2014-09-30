<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

use Brera\Lib\Queue\Backend\File\FileConfig;

/**
 * Class FileConfigTest
 *
 * @package Brera\Lib\Queue\Tests\Unit\Backend\File
 * @covers Brera\Lib\Queue\Backend\File\FileConfig
 */
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
     */
    public function testItImplementsTheBackendConfigInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\BackendConfigInterface', $this->config);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::setStorageRootDir
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::getStorageRootDir
     */
    public function testItReturnsTheSetStorageRootDir()
    {
        $this->config->setStorageRootDir('/dev/null');
        $this->assertEquals('/dev/null', $this->config->getStorageRootDir());
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::setKeepProcessedMessages
     * @covers Brera\Lib\Queue\Backend\File\FileConfig::getKeepProcessedMessages
     */
    public function testItReturnsKeepProcessedMessagesFlag()
    {
        $this->assertFalse($this->config->getKeepProcessedMessages());
        $this->config->setKeepProcessedMessages(true);
        $this->assertTrue($this->config->getKeepProcessedMessages());
    }
}
