<?php

namespace Brera\Lib\Queue\Tests\Integration;

use Brera\Lib\Queue\Backend\File\FileAbstractBackend;
use Brera\Lib\Queue\Backend\File\FileConfig,
    Brera\Lib\Queue\Backend\File\FileFactory,
    Brera\Lib\Queue\Factory;

class FileQueueBackendProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    private $tempDir = './tmp';

    public function setUp()
    {
        $this->factory = new Factory();
    }

    public function tearDown()
    {
        $this->removeTemporaryDir($this->tempDir);
    }

    /**
     * @test
     * @coversNothing
     */
    public function testBootstrapAndProcessFlowWithFileBackend()
    {
        $testPayloadValue = 'test-payload';
        $testChannelName = 'test-channel';

        $config = $this->factory->getRegisteredConfigInstance();
        $config->setBackendFactoryClass('Brera\Lib\Queue\Backend\File\FileFactory');
        $this->assertContains('FileFactory', $config->getBackendFactoryClass());

        /** @var \Brera\Lib\Queue\Backend\File\FileConfig $backendConfig */
        $backendConfig = $this->factory->getRegisteredBackendConfigInstance();
        $backendConfig->setStorageRootDir($this->tempDir);
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\File\FileConfig', $backendConfig);
        
        $producerQueue = $this->factory->getProducerQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerQueue', $producerQueue);
        
        $outgoingMessage = $producerQueue->sendMessageByChannel($testChannelName, $testPayloadValue);
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessage', $outgoingMessage);
        $this->assertEquals($testPayloadValue, $producerQueue->getMessagePayload($outgoingMessage));

        $consumerQueue = $this->factory->getConsumerQueue();
        $incomingMessage = $consumerQueue->receiveMessageFromChannel('test-channel');
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessage', $incomingMessage);
        $this->assertEquals($testPayloadValue, $consumerQueue->getMessagePayload($incomingMessage));

        $channelDir = $this->tempDir . DIRECTORY_SEPARATOR . $testChannelName;
        $completedDir = $channelDir . DIRECTORY_SEPARATOR . FileAbstractBackend::STATE_COMPLETED;
        $fileName = preg_replace('/.*\//', '', $incomingMessage->getIdentifier());

        $consumerQueue->setMessageAsProcessed($incomingMessage);
        $this->assertTrue(file_exists($completedDir . DIRECTORY_SEPARATOR . $fileName));
    }

    /**
     * @test
     * @coversNothing
     */
    public function testTheRegisteredBackendConfigInstanceIsSetOnTheBackendFactory()
    {
        /** @var FileFactory $backendConfig */
        $backendFactory = $this->factory->getRegisteredBackendFactoryInstance();

        /** @var FileConfig $backendConfig */
        $backendConfig = $this->factory->getRegisteredBackendConfigInstance();
        
        $this->assertAttributeSame($backendConfig, 'configuredBackendConfigInstance', $backendFactory);
    }

    private function removeTemporaryDir($dirPath)
    {
        if (file_exists($dirPath)){
            $files = array_diff(scandir($dirPath), array('.', '..'));

            foreach ($files as $file) {
                $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    $this->removeTemporaryDir($filePath);
                } else {
                    unlink($filePath);
                }
            }

            rmdir($dirPath);
        }
    }
}