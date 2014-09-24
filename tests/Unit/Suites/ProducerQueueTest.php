<?php


namespace Brera\Lib\Queue\Tests\Unit;


use Brera\Lib\Queue\ProducerQueue;

class ProducerQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProducerQueue
     */
    private $queue;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProducerAdapter;
    
    public function setUp()
    {
        $this->stubProducerAdapter = $this->getStubProducerAdapter();
        $this->queue = new ProducerQueue($this->stubProducerAdapter);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ProducerQueue::__construct
     */
    public function itShouldImplementTheProducerQueueInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\ProducerQueueInterface', $this->queue);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ProducerQueue::sendMessageByChannel
     */
    public function itShouldDelegateToTheAdapterToSendAMessage()
    {
        $this->stubProducerAdapter->expects($this->once())
            ->method('sendMessage');
        $this->queue->sendMessageByChannel('test-channel', 'test-payload');
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\ProducerQueue::sendMessageByChannel
     */
    public function itShouldReturnAnOutgoingMessage()
    {
        $stubMessage = $this->getStubOutgoingMessage();
        $this->stubProducerAdapter->expects($this->any())
            ->method('sendMessage')
            ->will($this->returnValue($stubMessage));
        
        $result = $this->queue->sendMessageByChannel('test-channel', 'test-payload');
        $this->assertSame($stubMessage, $result);
    }
    
    /**
     * @test
     * @covers Brera\Lib\Queue\ProducerQueue::getMessagePayload
     */
    public function itShouldReturnAMessagePayload()
    {
        $testPayloadValue = 'test-payload';
        
        $mockMessage = $this->getStubOutgoingMessage();
        $mockMessage->expects($this->once())
            ->method('getPayload')
            ->will($this->returnValue($testPayloadValue));
        $this->assertEquals($testPayloadValue, $this->queue->getMessagePayload($mockMessage));
    }
    

    private function getStubProducerAdapter()
    {
        $stubProducerAdapter = $this->getMockBuilder('Brera\Lib\Queue\ProducerAdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubProducerAdapter;
    }

    private function getStubOutgoingMessage()
    {
        $stubOutgoingMessage = $this->getMockBuilder('Brera\Lib\Queue\OutgoingMessageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubOutgoingMessage;
    }
}