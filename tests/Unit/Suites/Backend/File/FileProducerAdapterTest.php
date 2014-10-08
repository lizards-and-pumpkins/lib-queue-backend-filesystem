<?php

namespace Brera\Lib\Queue\Tests\Unit\Backend\File;

require_once __DIR__ . '/abstracts/AbstractTestFileAdapter.php';

use Brera\Lib\Queue\Backend\File\FileProducerAdapter;

/**
 * Class FileProducerAdapterTest
 *
 * @package Brera\Lib\Queue\Tests\Unit
 * @covers Brera\Lib\Queue\Backend\File\FileProducerAdapter
 */
class FileProducerAdapterTest extends AbstractTestFileAdapter
{
    /**
     * @var FileProducerAdapter
     */
    private $adapter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBackendImplementation;

    public function setUp()
    {
        $stubMessageBuilder = $this->getStubMessageBuilder();
        $stubBackendConfig = $this->getStubBackendConfig();
        $this->stubBackendImplementation = $this->getStubFileProducerBackend();
        $this->adapter = new FileProducerAdapter(
            $stubMessageBuilder, $stubBackendConfig, $this->stubBackendImplementation
        );
    }

    /**
     * @test
     * @covers Brera\Lib\Queue\Backend\File\FileProducerAdapter::sendMessageToBackend
     */
    public function itShouldSendAMessageViaTheBackendImplementation()
    {
        $rootDir = DIRECTORY_SEPARATOR . 'tmp';
        $channelName = 'test-channel';
        $fileName = 'foo';

        $messageIdentifier = $this->getMessageIdentifier($rootDir, $channelName, 'pending', $fileName);
        $this->stubBackendImplementation->expects($this->once())
            ->method('addMessageToQueue')
            ->will($this->returnValue($messageIdentifier));
        
        $this->adapter->sendMessageToBackend($channelName, 'test-message');
    }
}
