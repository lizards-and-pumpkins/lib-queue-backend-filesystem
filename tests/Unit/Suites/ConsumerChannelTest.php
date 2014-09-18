<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\ConsumerChannel;

class ConsumerChannelTest extends BaseTestCase
{
    /**
     * @var ConsumerChannel
     */
    private $channel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBackendAdapter;
    
    public function setUp()
    {
        $this->stubFactory = $this->getStubFactory();
        $this->stubBackendAdapter = $this->getStubBackendAdapter();
        $channelName = 'test-channel';
        $this->channel = new ConsumerChannel($this->stubFactory, $this->stubBackendAdapter, $channelName);
    }
    
    public function testItReturnsTheName()
    {
        $this->assertEquals('test-channel', $this->channel->getName());
    }
    
    public function testItReturnsAnIncomingMessage()
    {
        $this->addStubIncomingMessageToStubFactory($this->stubFactory);
        $result = $this->channel->receiveMessage();
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessageInterface', $result);
    }
    
    public function testItDelegatesToBackendAdapter()
    {
        $this->stubBackendAdapter->expects($this->once())
            ->method('receiveMessage');
        $this->channel->receiveMessage();
    }
} 