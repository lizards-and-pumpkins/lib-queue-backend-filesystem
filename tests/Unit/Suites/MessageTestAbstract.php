<?php


namespace Brera\Lib\Queue\Tests\Unit;


use Brera\Lib\Queue\MessageInterface;

abstract class MessageTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageInterface
     */
    protected $message;

    protected $testChannelName = 'test-channel';
    protected $testIdentifier = array('id' => 'dummy');
    protected $testPayload = 'test-payload';

    /**
     * @test
     */
    public function itShouldReturnTheMessageIdentifier()
    {
        $this->assertEquals($this->testIdentifier, $this->message->getIdentifier());
    }

    /**
     * @test
     */
    public function itShouldReturnThePayload()
    {
        $this->assertEquals($this->testPayload, $this->message->getPayload());
    }

    /**
     * @test
     */
    public function itShouldReturnTheChannelName()
    {
        $this->assertEquals($this->testChannelName, $this->message->getChannel());
    }
} 