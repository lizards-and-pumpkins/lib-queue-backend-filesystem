<?php

declare(strict_types = 1);

namespace LizardsAndPumpkins\Messaging\Queue\File;

use LizardsAndPumpkins\Messaging\Queue\Exception\NotSerializableException;
use LizardsAndPumpkins\Messaging\Queue\Message;
use LizardsAndPumpkins\Util\Storage\Clearable;

/**
 * @covers \LizardsAndPumpkins\Messaging\Queue\File\FileQueue
 */
class FileQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileQueue
     */
    private $fileQueue;

    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var string
     */
    private $lockFilePath;

    /**
     * @return FileQueue
     */
    private function createFileQueueInstance()
    {
        return new FileQueue($this->storagePath, $this->lockFilePath);
    }

    private function createTestMessage(string $name = 'dummy'): Message
    {
        return Message::withCurrentTime($name, '', []);
    }

    protected function setUp()
    {
        $this->storagePath = sys_get_temp_dir() . '/lizards-and-pumpkins/test-queue/content';
        $this->lockFilePath = sys_get_temp_dir() . '/lizards-and-pumpkins/test-queue/lock/lockfile';
        $this->fileQueue = $this->createFileQueueInstance();
    }

    protected function tearDown()
    {
        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
            rmdir(dirname($this->lockFilePath));
        }
        if (file_exists($this->storagePath)) {
            $list = scandir($this->storagePath);
            foreach ($list as $fileName) {
                $file = $this->storagePath . '/' . $fileName;
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->storagePath);
        }
    }

    public function testItStartsEmpty()
    {
        $this->assertSame(0, $this->fileQueue->count());
    }

    public function testItCountsOneAfterAddingOne()
    {
        $this->fileQueue->add($this->createTestMessage());
        $this->assertSame(1, $this->fileQueue->count());
    }

    public function testItIsNotReadyForNextWhenTheQueueIsEmpty()
    {
        $this->assertFalse($this->fileQueue->isReadyForNext());
    }

    public function testItIsReadyForNextWhenTheQueueIsNotEmpty()
    {
        $this->fileQueue->add($this->createTestMessage());
        $this->assertTrue($this->fileQueue->isReadyForNext());
    }

    public function testItThrowsAnExceptionWhenNextIsCalledOnEmptyQueue()
    {
        $this->expectException(\UnderflowException::class);
        $this->expectExceptionMessage('Trying to get next message of an empty queue');
        
        $this->fileQueue->next();
    }

    public function testAddsOneReturnsOne()
    {
        $message = $this->createTestMessage('foo bar');
        $this->fileQueue->add($message);
        $this->assertSame($message->getName(), $this->fileQueue->next()->getName());
    }

    public function testItDecrementsTheCountAfterCallingNext()
    {
        $this->fileQueue->add($this->createTestMessage());
        $this->fileQueue->add($this->createTestMessage());
        $this->fileQueue->add($this->createTestMessage());
        $this->assertSame(3, $this->fileQueue->count());
        $this->fileQueue->next();
        $this->assertSame(2, $this->fileQueue->count());
        $this->fileQueue->next();
        $this->assertSame(1, $this->fileQueue->count());
        $this->fileQueue->next();
        $this->assertSame(0, $this->fileQueue->count());
    }

    public function testAddOneTwoReturnsOneTwo()
    {
        $message1 = $this->createTestMessage('foo');
        $message2 = $this->createTestMessage('bar');
        $this->fileQueue->add($message1);
        $this->fileQueue->add($message2);
        $this->assertSame($message1->getName(), $this->fileQueue->next()->getName());
        $this->assertSame($message2->getName(), $this->fileQueue->next()->getName());
    }

    public function testAddOnOneInstanceRetrieveFromOtherInstance()
    {
        $stubMessage = $this->createTestMessage('foo');
        $this->fileQueue->add($stubMessage);
        $otherInstance = $this->createFileQueueInstance();
        $this->assertSame($stubMessage->getName(), $otherInstance->next()->getName());
    }

    public function testItReturnsManyMessagesInTheCorrectOrder()
    {
        $instanceOne = $this->fileQueue;
        $instanceTwo = $this->createFileQueueInstance();
        $nMessages = 1000;
        for ($i = 0; $i < $nMessages; $i++) {
            $writeQueue = $i % 2 === 0 ? $instanceOne : $instanceTwo;
            $writeQueue->add($this->createTestMessage('message_' . $i));
        }
        for ($i = 0; $i < $nMessages; $i++) {
            $readQueue = $i % 2 === 1 ? $instanceOne : $instanceTwo;
            $this->assertSame('message_' . $i, $readQueue->next()->getName());
        }
    }

    public function testItWillAppendASuffixIfTheFileAlreadyExists()
    {
        $testFileQueue = new FileNameFixtureFileQueue($this->storagePath, $this->lockFilePath, 'test-file');
        $testFileQueue->add($this->createTestMessage());
        $testFileQueue->add($this->createTestMessage());
        $testFileQueue->add($this->createTestMessage());
        $this->assertFileExists($this->storagePath . '/test-file');
        $this->assertFileExists($this->storagePath . '/test-file_1');
        $this->assertFileExists($this->storagePath . '/test-file_2');
    }

    public function testItIsClearable()
    {
        $this->assertInstanceOf(Clearable::class, $this->fileQueue);
    }

    public function testItClearsTheQueue()
    {
        $this->fileQueue->add($this->createTestMessage());
        $this->fileQueue->add($this->createTestMessage());
        $this->assertCount(2, $this->fileQueue);
        $this->fileQueue->clear();
        $this->assertCount(0, $this->fileQueue);
    }

    public function testItAddsTheMessageNameToTheFileNameMessages()
    {
        $stubMessage = $this->createTestMessage('foo_bar');
        $this->fileQueue->add($stubMessage);

        $pattern = '*-' . $stubMessage->getName();
        $message = sprintf('The message queue did not contain a file matching the pattern /%s', $pattern);

        $this->assertCount(1, glob($this->storagePath . '/' . $pattern), $message);
    }
}
