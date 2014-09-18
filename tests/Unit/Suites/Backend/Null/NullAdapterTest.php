<?php

namespace Brera\Lib\Queue\Tests\Unit;

require_once __DIR__ . '/../../../../../vendor/brera/lib-queue-interfaces/tests/Unit/Helper/BaseTestCase.php';

use Brera\Lib\Queue\Backend\Null\NullAdapter;

class NullAdapterTest extends BaseTestCase
{
    /**
     * @var NullAdapter
     */
    private $adapter;
    
    public function setUp()
    {
        $this->adapter = new NullAdapter();
    }
    
    public function testItReturnsAnEmptyStringForReceiveMessage()
    {
        $stubConsumerChannel = $this->getStubConsumerChannel();
        $result = $this->adapter->receiveMessage($stubConsumerChannel);
        $this->assertSame('', $result);
    }
} 