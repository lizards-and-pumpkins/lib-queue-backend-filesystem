<?php

namespace Brera\Lib\Queue\Tests\Integration;

use Brera\Lib\Queue\Backend\Null\NullConfig;
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
    
    public function testTheSoleBackendConfigInstanceIsSetOnTheBackendFactory()
    {
        /** @var NullConfig $backendConfig */
        $backendConfig = $this->factory->getRegisteredBackendConfigInstance();
        $backendFactory = $this->factory->getRegisteredBackendFactoryInstance();
        
        $this->assertAttributeSame($backendConfig, 'configuredBackendConfigInstance', $backendFactory);
    }
} 