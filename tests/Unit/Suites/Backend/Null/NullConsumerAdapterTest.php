<?php

namespace Brera\Lib\Queue\Tests\Unit;

use Brera\Lib\Queue\Backend\Null\NullConsumerAdapter;

class NullConsumerAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubMessageBuilder;
    
    /**
     * @var NullConsumerAdapter
     */
    private $adapter;
    
    public function setUp()
    {
        $this->stubMessageBuilder = $this->getStubMessageBuilder();
        $stubBackendConfig = $this->getStubBackendConfig();
        $this->adapter = new NullConsumerAdapter($this->stubMessageBuilder, $stubBackendConfig);
    }

    /**
     * @test
     */
    public function itShouldReturnAnEmptyIncomingMessage()
    {
        $stubIncomingMessage = $this->getStubIncomingMessage();
        
        $this->stubMessageBuilder->expects($this->once())->method('setPayload')->with('');
        $this->stubMessageBuilder->expects($this->once())->method('getIncomingMessage')
            ->will($this->returnValue($stubIncomingMessage));
        $message = $this->adapter->receiveMessageFromChannel('test-channel');
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessage', $message);
    }

    private function getStubMessageBuilder()
    {
        $stubMessageBuilder = $this->getMockBuilder('Brera\Lib\Queue\MessageBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubMessageBuilder;
    }
    
    private function getStubBackendConfig()
    {
        $stubBackendConfig = $this->getMockBuilder('Brera\Lib\Queue\Backend\Null\NullConfig')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubBackendConfig;
    }

    private function getStubIncomingMessage()
    {
        $stubIncomingMessage = $this->getMockBuilder('Brera\Lib\Queue\IncomingMessage')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubIncomingMessage;
    }
} 