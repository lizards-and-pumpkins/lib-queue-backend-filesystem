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

    /**
     * @test
     * @coversNothing
     */
    public function itShouldAllowUsingTwoQueuesInParallelWithoutAlwaysSwitchingTheFactoryRepository()
    {
        $repositoryA = $this->factory->getNewRepository();
        $repositoryB = $this->factory->getNewRepository();

        $this->switchToQueue($repositoryA);
        $this->configureQueue('A');
        $producerQueueA = $this->factory->getProducerQueue();
        $consumerQueueA = $this->factory->getConsumerQueue();

        $this->switchToQueue($repositoryB);
        $this->configureQueue('B');
        $producerQueueB = $this->factory->getProducerQueue();
        $consumerQueueB = $this->factory->getConsumerQueue();
        
        $producerQueueA->sendMessageByChannel('test-channel', 'A1');
        $message = $consumerQueueA->receiveMessageFromChannel('test-channel');
        $this->assertEquals('A1', $consumerQueueA->getMessagePayload($message));
        
        $producerQueueB->sendMessageByChannel('test-channel', 'B1');
        $message = $consumerQueueB->receiveMessageFromChannel('test-channel');
        $this->assertEquals('B1', $consumerQueueB->getMessagePayload($message));

        $producerQueueB->sendMessageByChannel('test-channel', 'B2');
        $producerQueueA->sendMessageByChannel('test-channel', 'A2');
        
        $message = $consumerQueueB->receiveMessageFromChannel('test-channel');
        $this->assertEquals('B2', $consumerQueueB->getMessagePayload($message));
        
        $message = $consumerQueueA->receiveMessageFromChannel('test-channel');
        $this->assertEquals('A2', $consumerQueueA->getMessagePayload($message));
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
