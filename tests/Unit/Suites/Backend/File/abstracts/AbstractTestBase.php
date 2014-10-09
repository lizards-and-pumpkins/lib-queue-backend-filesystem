<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

class AbstractTestBase extends \PHPUnit_Framework_TestCase
{
    protected function getStubMessageBuilder()
    {
        $stubMessageBuilder = $this->getMockBuilder('Brera\Lib\Queue\MessageBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubMessageBuilder;
    }

    protected function getStubBackendConfig()
    {
        $stubNullConfig = $this->getMockBuilder('Brera\Lib\Queue\Backend\File\FileConfig')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubNullConfig;
    }

    protected function getStubFactory()
    {
        $stubFactory = $this->getMockBuilder('Brera\Lib\Queue\Factory\ConfigurableQueueFactory')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFactory;
    }

    protected function getStubMessage($channelName, $payload, $identifier)
    {
        $stubMessage = $this->getMockBuilder('Brera\Lib\Queue\IncomingMessage')
            ->disableOriginalConstructor()
            ->getMock();
        $stubMessage->expects($this->any())
            ->method('getChannel')
            ->will($this->returnValue($channelName));
        $stubMessage->expects($this->any())
            ->method('getPayload')
            ->will($this->returnValue($payload));
        $stubMessage->expects($this->any())
            ->method('getIdentifier')
            ->will($this->returnValue($identifier));

        return $stubMessage;
    }

    protected function getMessageIdentifier($rootDir, $channelName, $state, $fileName)
    {
        $ds = DIRECTORY_SEPARATOR;

        return $rootDir . $ds . $channelName . $ds . $state . $ds . $fileName;
    }
}
