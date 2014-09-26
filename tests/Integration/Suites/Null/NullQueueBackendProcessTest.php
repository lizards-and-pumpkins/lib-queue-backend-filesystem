<?php

namespace Brera\Lib\Queue\Tests\Integration;

use Brera\Lib\Queue\Backend\Null\NullConfig;
use Brera\Lib\Queue\Backend\Null\NullFactory;
use Brera\Lib\Queue\Factory;

class BootstrapQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;
    
    public function setUp()
    {
        $this->factory = new Factory();
    }

    /**
     * @coversNothing
     */
    public function testBootstrapAndProcessFlowWithNullBackend()
    {
        $config = $this->factory->getRegisteredConfigInstance();
        $this->assertContains('NullFactory', $config->getBackendFactoryClass());
        
        $backendConfig = $this->factory->getRegisteredBackendConfigInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConfig', $backendConfig);
        
        $producerQueue = $this->factory->getProducerQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerQueue', $producerQueue);
        
        $testPayloadValue = 'test-payload';
        $outgoingMessage = $producerQueue->sendMessageByChannel('test-channel', $testPayloadValue);
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessage', $outgoingMessage);
        
        $this->assertEquals($testPayloadValue, $producerQueue->getMessagePayload($outgoingMessage));
        
        $consumerQueue = $this->factory->getConsumerQueue();
        
        $incomingMessage = $consumerQueue->receiveMessageFromChannel('test-channel');
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessage', $incomingMessage);

        $this->assertEquals('', $consumerQueue->getMessagePayload($incomingMessage));
        
        $consumerQueue->setMessageAsProcessed($incomingMessage);
        
    }

    /**
     * @coversNothing
     */
    public function testTheRegisteredBackendConfigInstanceIsSetOnTheBackendFactory()
    {
        /** @var NullConfig $backendConfig */
        /** @var NullFactory $backendConfig */
        $backendFactory = $this->factory->getRegisteredBackendFactoryInstance();
        $backendConfig = $this->factory->getRegisteredBackendConfigInstance();
        
        $this->assertAttributeSame($backendConfig, 'configuredBackendConfigInstance', $backendFactory);
    }
} 