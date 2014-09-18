<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\ProducerChannel;

class ProducerChannelTest extends BaseTestCase
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
     * @var ProducerChannel
     */
    private $channel;
    
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->stubBackendAdapter = $this->getStubBackendAdapter();
        $channelName = 'test-channel';
        $this->channel = new ProducerChannel($this->stubFactory, $this->stubBackendAdapter, $channelName);
    }
    
    public function testItReturnsTheName()
    {
        $this->assertEquals('test-channel', $this->channel->getName());
    }

    public function testItReturnsAnOutgoingPayload()
    {
        $this->addStubOutgoingMessageToStubFactory($this->stubFactory);
        $result = $this->channel->createOutgoingMessage('test');
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessageInterface', $result);
    }

    /**
     * @dataProvider getValidSendMessagePayloads
     */
    public function testItDelegatesToAdapterToSendMessage($payload)
    {
        $this->addStubOutgoingMessageToStubFactory($this->stubFactory);
        $this->stubBackendAdapter->expects($this->once())
            ->method('sendMessage');
        $this->channel->sendMessage($payload);
    }
    
    public function getValidSendMessagePayloads()
    {
        return array(
            array('test'),
            array($this->getStubOutgoingMessage())
        );
    }
    
    /**
     * @dataProvider getInvalidSendMessagePayloads
     * @expectedException \InvalidArgumentException
     */
    public function testItThrowsIfSendMessageArgumentIsInvalidType($invalidPayload)
    {
        $this->addStubOutgoingMessageToStubFactory($this->stubFactory);
        $this->channel->sendMessage($invalidPayload);
    }
    
    public function getInvalidSendMessagePayloads()
    {
        return array(
            array(new \stdClass()),
            array(null),
            array(array()),
            array(true),
            array(1234),
            array(0.1),
        );
    }
} 