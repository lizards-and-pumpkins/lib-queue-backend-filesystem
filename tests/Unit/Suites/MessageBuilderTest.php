<?php


namespace Brera\Lib\Queue\Tests\Unit;

use Brera\Lib\Queue\MessageBuilder;

class MessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageBuilder
     */
    private $messageBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;

    /**
     *
     */
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->messageBuilder = new MessageBuilder($this->stubFactory);
    }

    /**
     * @test
     */
    public function theIdentifierShouldBeSettable()
    {
        $testIdentifierValue = array('test-id');
        $this->messageBuilder->setIdentifier($testIdentifierValue);
        $this->assertAttributeSame($testIdentifierValue, 'identifier', $this->messageBuilder);
    }

    /**
     * @test
     */
    public function thePayloadShouldBeSettable()
    {
        $testPayloadValue = 'test-payload';
        $this->messageBuilder->setPayload($testPayloadValue);
        $this->assertAttributeSame($testPayloadValue, 'payload', $this->messageBuilder);
    }

    /**
     * @test
     */
    public function theChannelShouldBeSettable()
    {
        $testChannelName = 'test-channel';
        $this->messageBuilder->setChannel($testChannelName);
        $this->assertAttributeSame($testChannelName, 'channel', $this->messageBuilder);
    }

    /**
     * @test
     */
    public function itShouldCopyAllValuesFromAGivenMessage()
    {
        $testIdentifierValue = (object) array('id' => 'test-id');
        $testPayloadValue = 'test-payload';
        $testChannelName = 'test-channel';
        $stubMessage = $this->getStubMessageWithValues($testChannelName, $testPayloadValue, $testIdentifierValue);
        
        $this->messageBuilder->initializeFromMessage($stubMessage);
        $this->assertAttributeSame($testIdentifierValue, 'identifier', $this->messageBuilder);
        $this->assertAttributeSame($testPayloadValue, 'payload', $this->messageBuilder);
        $this->assertAttributeSame($testChannelName, 'channel', $this->messageBuilder);
    }

    /**
     * @test
     */
    public function itShouldReturnAnIncomingMessage()
    {
        $this->setStubIncomingMessageOnFactory();
        $result = $this->messageBuilder->getIncomingMessage();
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessageInterface', $result);
    }

    /**
     * @test
     */
    public function itShouldReturnAnOutgoingMessage()
    {
        $this->setStubOutgoingMessageOnFactory();
        $result = $this->messageBuilder->getOutgoingMessage();
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessageInterface', $result);
    }
    
    private function getStubFactory()
    {
        $stubFactory = $this->getMockBuilder('Brera\Lib\Queue\FactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFactory;
    }

    private function getStubMessageWithValues($testChannelName, $testPayloadValue, $testIdentifierValue)
    {
        $stubMessage = $this->getStubMessage();
        
        $this->setStubMethodAndReturnValueOnStub($stubMessage, 'getIdentifier', $testIdentifierValue);
        $this->setStubMethodAndReturnValueOnStub($stubMessage, 'getPayload', $testPayloadValue);
        $this->setStubMethodAndReturnValueOnStub($stubMessage, 'getChannel', $testChannelName);
        
        return $stubMessage;
    }

    private function setStubMethodAndReturnValueOnStub(
        \PHPUnit_Framework_MockObject_MockObject $stubObject, $methodName, $returnValue
    )
    {
        $stubObject->expects($this->any())
            ->method($methodName)
            ->will($this->returnValue($returnValue));
    }

    private function setStubIncomingMessageOnFactory()
    {
        $stubIncomingMessage = $this->getStubMessage('incoming');
        $this->stubFactory->expects($this->any())
            ->method('getIncomingMessage')
            ->will($this->returnValue($stubIncomingMessage));
        return $stubIncomingMessage;
    }

    private function setStubOutgoingMessageOnFactory()
    {
        $stuboutgoingMessage = $this->getStubMessage('outgoing');
        $this->stubFactory->expects($this->any())
            ->method('getOutgoingMessage')
            ->will($this->returnValue($stuboutgoingMessage));
        return $stuboutgoingMessage;
    }

    private function getStubMessage($type = '')
    {
        $type = ucfirst($type);
        $stubMessage = $this->getMockBuilder('Brera\Lib\Queue\\' . $type . 'MessageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubMessage;
    }
} 