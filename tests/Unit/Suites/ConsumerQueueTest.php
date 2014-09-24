<?php


namespace Brera\Lib\Queue\Tests\Unit;

use Brera\Lib\Queue\ConsumerQueue;

class ConsumerQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConsumerQueue;
     */
    private $queue;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubConsumerAdapter;

    public function setUp()
    {
        $this->stubConsumerAdapter = $this->getStubConsumerAdapter();
        $this->queue = new ConsumerQueue($this->stubConsumerAdapter);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ConsumerQueue::__construct
     */
    public function itShouldImplementTheConsumerQueueInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\ConsumerQueueInterface', $this->queue);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ConsumerQueue::receiveMessageFromChannel
     */
    public function itShouldDelegateToTheAdapterToReceiveAMessage()
    {
        $this->stubConsumerAdapter->expects($this->once())
            ->method('receiveMessageFromChannel');
        $this->queue->receiveMessageFromChannel('test-channel');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ConsumerQueue::receiveMessageFromChannel
     */
    public function itShouldReturnAnIncomingMessage()
    {
        $stubMessage = $this->getStubIncomingMessage();
        $this->stubConsumerAdapter->expects($this->once())
            ->method('receiveMessageFromChannel')
            ->will($this->returnValue($stubMessage));
        $result = $this->queue->receiveMessageFromChannel('test-channel');
        $this->assertSame($stubMessage, $result);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ConsumerQueue::setMessageAsProcessed
     */
    public function itShouldDelegateToTheAdapterToSetAMessageAsProcessed()
    {
        $stubMessage = $this->getStubIncomingMessage();
        $this->stubConsumerAdapter->expects($this->once())
            ->method('setMessageAsProcessed')
            ->with($stubMessage);
        $this->queue->setMessageAsProcessed($stubMessage);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ConsumerQueue::getMessagePayload
     */
    public function itShouldReturnAMessagePayload()
    {
        $testPayloadValue = 'test-payload';
        $mockMessage = $this->getStubIncomingMessage();
        $mockMessage->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($testPayloadValue));
        
        $result = $this->queue->getMessagePayload($mockMessage);
        $this->assertEquals($testPayloadValue, $result);
    }

    private function getStubConsumerAdapter()
    {
        $stubConsumerAdapter = $this->getMockBuilder('Brera\Lib\Queue\ConsumerAdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubConsumerAdapter;
    }

    private function getStubIncomingMessage()
    {
        $stubIncomingMessage = $this->getMockBuilder('Brera\Lib\Queue\IncomingMessageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubIncomingMessage;
    }
} 