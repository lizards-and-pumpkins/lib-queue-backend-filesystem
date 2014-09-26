<?php

namespace Brera\Lib\Queue\Tests\Unit;

include_once __DIR__ . '/abstracts/AbstractTestBase.php';

use Brera\Lib\Queue\Backend\File\FileFactory;

/**
 * Class FileFactoryTest
 *
 * @package Brera\Lib\Queue\Tests\Unit
 * @covers Brera\Lib\Queue\Backend\File\FileFactory
 */
class FileFactoryTest extends AbstractTestBase
{
    /**
     * @var FileFactory
     */
    private $backendFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;

    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->backendFactory = new FileFactory($this->stubFactory);
        $this->backendFactory->setConfiguredBackendConfigInstance($this->getStubBackendConfig());
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getNewBackendConfig
     */
    public function testItReturnsAFileConfigInstance()
    {
        $result = $this->backendFactory->getNewBackendConfig();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileConfig', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getConsumerAdapter
     */
    public function testItReturnsAFileConsumerAdapter()
    {
        $this->setStubMessageBuilderOnStubFactory();
        $result = $this->backendFactory->getConsumerAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileConsumerAdapter', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getProducerAdapter
     */
    public function testItReturnsAFileProducerAdapter()
    {
        $this->setStubMessageBuilderOnStubFactory();
        $result = $this->backendFactory->getProducerAdapter();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileProducerAdapter', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getConsumerBackend
     */
    public function itShouldReturnAConsumerBackendImplementation()
    {
        $result = $this->backendFactory->getConsumerBackend();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileConsumerBackend', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getProducerBackend
     */
    public function itShouldReturnAProducerBackendImplementation()
    {
        $result = $this->backendFactory->getProducerBackend();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileProducerBackend', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getFilesystemDirectoryInstance
     */
    public function testItReturnsAFilesystemDirectory()
    {
        $result = $this->backendFactory->getFilesystemDirectoryInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\Filesystem\Directory', $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileFactory::getFilesystemFileInstance
     */
    public function testItReturnsAFilesystemFile()
    {
        $result = $this->backendFactory->getFilesystemFileInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\Filesystem\File', $result);
    }

    private function setStubMessageBuilderOnStubFactory()
    {
        $this->stubFactory->expects($this->any())
            ->method('getMessageBuilder')
            ->will($this->returnCallback(function() {
                return $this->getStubMessageBuilder();
            }));
    }
}
