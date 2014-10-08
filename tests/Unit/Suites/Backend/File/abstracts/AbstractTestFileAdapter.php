<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

require_once __DIR__ . '/AbstractTestBase.php';

class AbstractTestFileAdapter extends AbstractTestBase {

    protected function getStubFileConsumerBackend()
    {
        $stubConsumerBackend = $this->getMockBuilder('Brera\Lib\Queue\Backend\File\FileConsumerBackend')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubConsumerBackend;
    }

    protected function getStubFileProducerBackend()
    {
        $stubProducerBackend = $this->getMockBuilder('Brera\Lib\Queue\Backend\File\FileProducerBackend')
            ->disableOriginalConstructor()
            ->getMock();
        return $stubProducerBackend;
    }
}
