<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\Queue;

class QueueTest extends BaseTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBackendAdapter;
    
    /**
     * @var Queue
     */
    private $queue;
    
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->stubBackendAdapter = $this->getStubBackendAdapter();
        $this->queue = new Queue($this->stubFactory, $this->stubBackendAdapter);
    }
    
    public function testItReturnsAProducerChannel()
    {
        $this->addStubProducerChannelToStubFactory($this->stubFactory);
        $result = $this->queue->getProducerChannel('test');
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerChannelInterface', $result);
    }
    
    public function testItReturnsTheSameProducerChannelForTheSameName()
    {
        $this->addStubProducerChannelToStubFactory($this->stubFactory);
        $result1 = $this->queue->getProducerChannel('test');
        $result2 = $this->queue->getProducerChannel('test');
        $this->assertSame($result1, $result2);
    }
    
    public function testItReturnsDifferentProducerChannelsForDifferentNames()
    {
        $this->addStubProducerChannelToStubFactory($this->stubFactory);
        $result1 = $this->queue->getProducerChannel('test-channel-a');
        $result2 = $this->queue->getProducerChannel('test-channel-b');
        $this->assertNotSame($result1, $result2);
    }

    public function testItReturnsAConsumerChannel()
    {
        $this->addStubConsumerChannelToStubFactory($this->stubFactory);
        $result = $this->queue->getConsumerChannel('test');
        $this->assertInstanceOf('Brera\Lib\Queue\ConsumerChannelInterface', $result);
    }

    public function testItReturnsTheSameConsumerChannelForTheSameName()
    {
        $this->addStubConsumerChannelToStubFactory($this->stubFactory);
        $result1 = $this->queue->getConsumerChannel('test');
        $result2 = $this->queue->getConsumerChannel('test');
        $this->assertSame($result1, $result2);
    }

    public function testItReturnsDifferentConsumerChannelsForDifferentNames()
    {
        $this->addStubConsumerChannelToStubFactory($this->stubFactory);
        $result1 = $this->queue->getConsumerChannel('test-channel-a');
        $result2 = $this->queue->getConsumerChannel('test-channel-b');
        $this->assertNotSame($result1, $result2);
    }
} 