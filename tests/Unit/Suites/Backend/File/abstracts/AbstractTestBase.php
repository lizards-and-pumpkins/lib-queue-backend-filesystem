<?php

namespace Brera\Lib\Queue\Tests\Unit;

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
        $stubFactory = $this->getMockBuilder('Brera\Lib\Queue\Factory')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubFactory;
    }
}