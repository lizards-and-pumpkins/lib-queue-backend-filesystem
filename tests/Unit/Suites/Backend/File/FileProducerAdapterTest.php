<?php

namespace Brera\Lib\Queue\Tests\Unit;

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
        $this->stubBackendImplementation->expects($this->any())
            ->method('addMessageToQueue')
            ->will($this->returnValue('/dev/null/test-channel/pending/foo'));
        
        $result = $this->adapter->sendMessageToBackend('test-channel', 'test-message');

        $this->assertEquals('/dev/null/test-channel/pending/foo', $result);
    }
}
 