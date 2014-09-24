<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/MessageTestAbstract.php';

use Brera\Lib\Queue\OutgoingMessage;

class OutgoingMessageTest extends MessageTestAbstract
{
    public function setUp()
    {
        $this->message = new OutgoingMessage($this->testChannelName, $this->testPayload, $this->testIdentifier);
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\OutgoingMessage::__construct
     */
    public function itShouldImplementTheOutgoingMessageInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\OutgoingMessageInterface', $this->message);
    }
} 