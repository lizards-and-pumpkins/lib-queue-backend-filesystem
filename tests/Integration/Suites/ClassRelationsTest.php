<?php

namespace Brera\Lib\Queue\Tests\Integration;

use Brera\Lib\Queue\Factory\FileQueueFactory,
    Brera\Lib\Queue\Backend\File\FileConfig;

class ClassRelationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileQueueFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new FileQueueFactory();
    }

    /**
     * @test
     * @coversNothing
     */
    public function theQueueBackendInUseShouldBeExchangeableViaTheRegistryInstance()
    {
        $messageA = 'Message A';
        $messageB = 'Message B';
        
        $repositoryA = $this->factory->getNewRepository();
        $repositoryB = $this->factory->getNewRepository();

        $this->switchToQueue($repositoryA);
        $this->configureQueue('A');
        $this->sendMessageOnActiveQueue($messageA);

        $this->switchToQueue($repositoryB);
        $this->configureQueue('B');
        $this->sendMessageOnActiveQueue($messageB);

        $messagePayload = $this->consumeMessageFromActiveQueue();
        $this->assertEquals($messageB, $messagePayload);
        
        $this->switchToQueue($repositoryA);
        
        $messagePayload = $this->consumeMessageFromActiveQueue();
        $this->assertEquals($messageA, $messagePayload);
    }

    private function configureQueue($identifier)
    {
        /** @var FileConfig $config */
        $config = $this->factory->getRegisteredBackendConfigInstance();
        $config->setStorageRootDir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $identifier);
        $config->getKeepProcessedMessages(false);
    }

    private function sendMessageOnActiveQueue($payload)
    {
        $producer = $this->factory->getProducerQueue();
        $producer->sendMessageByChannel('test-channel', $payload);
    }

    private function consumeMessageFromActiveQueue()
    {
        $consumer = $this->factory->getConsumerQueue();
        $message = $consumer->receiveMessageFromChannel('test-channel');
        $messagePayload = $consumer->getMessagePayload($message);
        return $messagePayload;
    }

    private function switchToQueue($repository)
    {
        $this->factory->setRepository($repository);
    }
}
