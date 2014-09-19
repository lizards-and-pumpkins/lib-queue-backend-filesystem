<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\IncomingMessage;

class IncomingMessageTest extends BaseTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubChannel;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBackendAdapter;
    
    /**
     * @var IncomingMessage
     */
    private $message;
    
    public function setUp()
    {
        $this->stubChannel = $this->getStubConsumerChannel();
        $this->stubBackendAdapter = $this->getStubBackendAdapter();
        $payload = 'test-payload';
        $this->message = new IncomingMessage($this->stubChannel, $this->stubBackendAdapter, $payload);
    }
    
    public function testItReturnsTheSetIdentifier()
    {
        $value = 'test message id';
        $this->message->setIdentifier($value);
        $this->assertEquals($value, $this->message->getIdentifier());
    }
    
    public function testItReturnsThePayload()
    {
        $this->assertEquals('test-payload', $this->message->getPayload());
    }
    
    public function testItDelegatesToTheBackendAdapterToSetAMessageAsProcessed()
    {
        $this->stubBackendAdapter->expects($this->once())
            ->method('confirmMessageIsProcessed');
        $this->message->setAsProcessed();
    }
} 