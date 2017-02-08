<?php

declare(strict_types = 1);

namespace LizardsAndPumpkins\Messaging\Queue\File;

use LizardsAndPumpkins\Messaging\MessageReceiver;
use LizardsAndPumpkins\Messaging\Queue\File\Exception\MessageCanNotBeStoredException;
use LizardsAndPumpkins\Messaging\Queue\Message;
use LizardsAndPumpkins\Util\Storage\Clearable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\Callback;

/**
 * @covers \LizardsAndPumpkins\Messaging\Queue\File\FileQueue
 */
class FileQueueTest extends TestCase
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
     * @var MessageReceiver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMessageReceiver;

    /**
     * @var bool
     */
    private static $diskIsFull;
    
    /**
     * @var int
     */
    private $addTestMessageAtMicrotime = 0;

    /**
     * @var int[]
     */
    public static $flockingActions = [];

    public static function isDiskFull(): bool
    {
        return self::$diskIsFull;
    }
    
    private function createFileQueueInstance(): FileQueue
    {
        return new FileQueue($this->storagePath, $this->lockFilePath);
    }

    private function createTestMessage(): Message
    {
        return $this->createTestMessageWithName('dummy');
    }

    private function createTestMessageWithName(string $name): Message
    {
        return Message::withCurrentTime($name, [], []);
    }

    private function isMessageWithName(string $name): Callback
    {
        return $this->callback(function (Message $receivedMessage) use ($name) {
            return $name === $receivedMessage->getName();
        });
    }

    private function clearTestQueueStorage()
    {
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

    public function addTestMessageTickCallback()
    {
        if ($this->addTestMessageAtMicrotime > 0 && microtime(true) > $this->addTestMessageAtMicrotime) {
            $this->fileQueue->add($this->createTestMessage());
            $this->addTestMessageAtMicrotime = 0;
        }
    }

    private function addTestMessageInSeconds($secs)
    {
        $this->addTestMessageAtMicrotime = microtime(true) + $secs;
    }

    protected function setUp()
    {
        declare(ticks = 1);
        register_tick_function([$this, 'addTestMessageTickCallback']);
        self::$diskIsFull = false;
        self::$flockingActions = [];

        $this->storagePath = sys_get_temp_dir() . '/lizards-and-pumpkins/test-queue/content';
        $this->clearTestQueueStorage();
        $this->lockFilePath = sys_get_temp_dir() . '/lizards-and-pumpkins/test-queue/lock/lockfile';
        $this->fileQueue = $this->createFileQueueInstance();
        $this->mockMessageReceiver = $this->createMock(MessageReceiver::class);
    }

    protected function tearDown()
    {
        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
            rmdir(dirname($this->lockFilePath));
        }
        $this->clearTestQueueStorage();
        unregister_tick_function([$this, 'addTestMessageTickCallback']);
    }

    public function testExceptionIsThrownIfStoragePathIsNotAString()
    {
        $this->expectException(\TypeError::class);
        new FileQueue([], '');
    }

    public function testExceptionIsThrownIfLockFilePathIsNotAString()
    {
        $this->expectException(\TypeError::class);
        new FileQueue('', []);
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

    public function testAddsOneReturnsOne()
    {
        $sourceMessage = $this->createTestMessageWithName('foo bar');
        $this->fileQueue->add($sourceMessage);
        $this->mockMessageReceiver->expects($this->once())->method('receive')
            ->with($this->isMessageWithName($sourceMessage->getName()));
        $this->fileQueue->consume($this->mockMessageReceiver, 1);
    }

    public function testItDecrementsTheCountAfterCallingNext()
    {
        $this->fileQueue->add($this->createTestMessage());
        $this->fileQueue->add($this->createTestMessage());
        $this->fileQueue->add($this->createTestMessage());
        $this->assertSame(3, $this->fileQueue->count());
        $this->fileQueue->consume($this->mockMessageReceiver, 1);
        $this->assertSame(2, $this->fileQueue->count());
        $this->fileQueue->consume($this->mockMessageReceiver, 1);
        $this->assertSame(1, $this->fileQueue->count());
        $this->fileQueue->consume($this->mockMessageReceiver, 1);
        $this->assertSame(0, $this->fileQueue->count());
    }

    public function testAddOneTwoReturnsOneTwo()
    {
        $message1 = $this->createTestMessageWithName('foo');
        $message2 = $this->createTestMessageWithName('bar');
        $this->fileQueue->add($message1);
        $this->fileQueue->add($message2);
        $this->mockMessageReceiver->expects($this->exactly(2))->method('receive')
            ->withConsecutive(
                [$this->isMessageWithName($message1->getName())],
                [$this->isMessageWithName($message2->getName())]
            );
        $this->fileQueue->consume($this->mockMessageReceiver, 2);
    }

    public function testAddOnOneInstanceRetrieveFromOtherInstance()
    {
        $testMessage = $this->createTestMessageWithName('foo');
        $this->fileQueue->add($testMessage);
        $otherInstance = $this->createFileQueueInstance();
        $this->mockMessageReceiver->expects($this->once())->method('receive')
            ->with($this->isMessageWithName($testMessage->getName()));
        $otherInstance->consume($this->mockMessageReceiver, 1);
    }

    public function testItReturnsManyMessagesInTheCorrectOrder()
    {
        $instanceOne = $this->fileQueue;
        $instanceTwo = $this->createFileQueueInstance();
        $nMessages = 1000;
        for ($i = 0; $i < $nMessages; $i++) {
            $writeQueue = $i % 2 === 0 ? $instanceOne : $instanceTwo;
            $writeQueue->add($this->createTestMessageWithName('message_' . $i));
        }

        $receiveCallCount = 0;
        $this->mockMessageReceiver->expects($this->exactly($nMessages))->method('receive')
            ->with($this->callback(function (Message $message) use (&$receiveCallCount, $nMessages) {
                $expected = 'message_' . $receiveCallCount++;
                if ($receiveCallCount > $nMessages) {
                    // Workaround https://github.com/sebastianbergmann/phpunit-mock-objects/issues/261
                    $expected = 'message_' . ($receiveCallCount - 2);
                }

                return $expected === $message->getName();
            }));

        for ($i = 0; $i < $nMessages; $i++) {
            $readQueue = $i % 2 === 1 ? $instanceOne : $instanceTwo;
            $readQueue->consume($this->mockMessageReceiver, 1);
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
        $testMessage = $this->createTestMessageWithName('foo_bar');
        $this->fileQueue->add($testMessage);

        $pattern = '*-' . $testMessage->getName();
        $message = sprintf('The message queue did not contain a file matching the pattern /%s', $pattern);

        $this->assertCount(1, glob($this->storagePath . '/' . $pattern), $message);
    }

    public function testExceptionIsThrownIfMessageCouldNotBeWritten()
    {
        self::$diskIsFull = true;
        $this->expectException(MessageCanNotBeStoredException::class);
        $this->fileQueue->add($this->createTestMessageWithName('foo_bar'));
    }

    public function testReleasesLockBeforeMessageConsumerIsCalled()
    {
        $this->fileQueue->add($this->createTestMessage());
        $this->fileQueue->consume(new class($this) implements MessageReceiver {
            private $test;

            public function __construct(TestCase $test)
            {
                $this->test = $test;
            }
            public function receive(Message $message)
            {
                $message = sprintf('Failing asserting that the flocking operations match the expected actions');
                $lastLockingAction = end(FileQueueTest::$flockingActions);
                $this->test->assertSame(\LOCK_UN, $lastLockingAction, $message);
            }
        }, 1);
        
    }
}

/**
 * @param string $filename
 * @param mixed $data
 * @param int $flags
 * @param resource|null $context
 * @return int|bool
 */
function file_put_contents(string $filename, $data, int $flags = 0, $context = null)
{
    if (FileQueueTest::isDiskFull()) {
        return false;
    }

    return \file_put_contents($filename, $data, $flags, $context);
}

function flock($handle, int $operation, &$wouldblock = null)
{
    FileQueueTest::$flockingActions[] = $operation;
    return \flock($handle, $operation, $wouldblock);
}
