<?php


namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/MessageTestAbstract.php';

use Brera\Lib\Queue\IncomingMessage;

class IncomingMessageTest extends MessageTestAbstract
{
    public function setUp()
    {
        $this->message = new IncomingMessage($this->testChannelName, $this->testPayload, $this->testIdentifier);
    }

    /**
     * @test
     */
    public function itShouldImplementTheIncomingMessageInterface()
    {
        $this->assertInstanceOf('Brera\Lib\Queue\IncomingMessageInterface', $this->message);
    }
}