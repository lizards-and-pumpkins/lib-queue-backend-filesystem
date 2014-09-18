<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\OutgoingMessage;


class OutgoingMessageTest extends BaseTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBackendAdapter;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProducerChannel;
    
    /**
     * @var OutgoingMessage
     */
    private $message;
    
    public function setUp()
    {
        $this->stubProducerChannel = $this->getStubProducerChannel();
        $this->stubBackendAdapter = $this->getStubBackendAdapter();
        $payload = 'test-payload';
        $this->message = new OutgoingMessage($this->stubProducerChannel, $this->stubBackendAdapter, $payload);
    }
    
    public function testItReturnsThePayload()
    {
        $this->assertEquals('test-payload', $this->message->getPayload());
    }
} 