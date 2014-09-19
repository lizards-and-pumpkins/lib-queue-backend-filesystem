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
        $config = $this->factory->getSoleConfigInstance();
        $this->assertContains('NullFactory', $config->getBackendFactoryClass());
        
        $backendConfig = $this->factory->getSoleBackendConfigInstance();
        $this->assertInstanceOf('Brera\Lib\Queue\Backend\Null\NullConfig', $backendConfig);
        
        $queue = $this->factory->getQueue();
        $this->assertInstanceOf('Brera\Lib\Queue\Queue', $queue);
        
        $producerChannel = $queue->getProducerChannel('test');
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerChannel', $producerChannel);

        $outgoingMessage = $producerChannel->createOutgoingMessage('test1');
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessage', $outgoingMessage);
        
        // Run sendMessage with each valid argument type (OutgoingMessage and string)
        $producerChannel->sendMessage($outgoingMessage);
        $producerChannel->sendMessage('test2');
        
        $consumerChannel = $queue->getConsumerChannel('test');
        $this->assertInstanceOf('Brera\Lib\Queue\ConsumerChannel', $consumerChannel);
        
        $incomingMessage = $consumerChannel->receiveMessage();
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessage', $incomingMessage);
    }
    
    public function testTheSoleBackendConfigInstanceIsSetOnTheBackendFactory()
    {
        /** @var NullConfig $backendConfig */
        $backendConfig = $this->factory->getSoleBackendConfigInstance();
        $backendFactory = $this->factory->getSoleBackendFactoryInstance();
        
        $this->assertAttributeSame($backendConfig, 'configuredBackendConfigInstance', $backendFactory);
    }
} 