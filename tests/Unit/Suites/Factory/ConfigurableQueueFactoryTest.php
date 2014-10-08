<?php


namespace Brera\Lib\Queue\Tests\Unit\Factory;

use Brera\Lib\Queue\Factory\ConfigurableQueueFactory;

require_once __DIR__ . '/QueueFactoryTestAbstract.php';

class ConfigurableFactoryTest extends QueueFactoryTestAbstract
{
    protected function getInstance()
    {
        return new ConfigurableQueueFactory();
    }
} 