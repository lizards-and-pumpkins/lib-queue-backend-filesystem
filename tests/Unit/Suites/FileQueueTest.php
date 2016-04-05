<?php

namespace LizardsAndPumpkins\Queue\File;

use LizardsAndPumpkins\Queue\NotSerializableException;
use LizardsAndPumpkins\Utils\Clearable;

/**
 * @covers \LizardsAndPumpkins\Queue\File\FileQueue
 * @uses   \LizardsAndPumpkins\Utils\LocalFilesystem
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
        $this->fileQueue->add('dummy');
        $this->assertSame(1, $this->fileQueue->count());
    }

    public function testItIsNotReadyForNextWhenTheQueueIsEmpty()
    {
        $this->assertFalse($this->fileQueue->isReadyForNext());
    }

    public function testItIsReadyForNextWhenTheQueueIsNotEmpty()
    {
        $this->fileQueue->add('dummy');
        $this->assertTrue($this->fileQueue->isReadyForNext());
    }

    public function testItThrowsAnExceptionWhenNextIsCalledOnEmptyQueue()
    {
        $this->setExpectedException(\UnderflowException::class, 'Trying to get next message of an empty queue');
        $this->fileQueue->next();
    }

    public function testAddsOneReturnsOne()
    {
        $value = 'test dummy';
        $this->fileQueue->add($value);
        $this->assertSame($value, $this->fileQueue->next());
    }

    public function testItDecrementsTheCountAfterCallingNext()
    {
        $this->fileQueue->add('message');
        $this->fileQueue->add('message');
        $this->fileQueue->add('message');
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
        $value1 = 'one';
        $value2 = 'two';
        $this->fileQueue->add($value1);
        $this->fileQueue->add($value2);
        $this->assertSame($value1, $this->fileQueue->next());
        $this->assertSame($value2, $this->fileQueue->next());
    }

    public function testItThrowsNotSerializableException()
    {
        $simpleXml = simplexml_load_string('<root />');
        $this->setExpectedException(NotSerializableException::class);
        $this->fileQueue->add($simpleXml);
    }

    public function testAddOnOneInstanceRetrieveFromOtherInstance()
    {
        $value = 'one';
        $this->fileQueue->add($value);
        $otherInstance = $this->createFileQueueInstance();
        $this->assertSame($value, $otherInstance->next());
    }

    public function testItReturnsManyMessagesInTheCorrectOrder()
    {
        $instanceOne = $this->fileQueue;
        $instanceTwo = $this->createFileQueueInstance();
        $nMessages = 1000;
        for ($i = 0; $i < $nMessages; $i++) {
            $writeQueue = $i % 2 === 0 ? $instanceOne : $instanceTwo;
            $writeQueue->add($i);
        }
        for ($i = 0; $i < $nMessages; $i++) {
            $readQueue = $i % 2 === 1 ? $instanceOne : $instanceTwo;
            $this->assertSame($i, $readQueue->next());
        }
    }

    public function testItWillAppendASuffixIfTheFileAlreadyExists()
    {
        require_once 'FileNameFixtureFileQueue.php';
        $testFileQueue = new FileNameFixtureFileQueue($this->storagePath, $this->lockFilePath, 'test-file');
        $testFileQueue->add('message');
        $testFileQueue->add('message');
        $testFileQueue->add('message');
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
        $this->fileQueue->add('one');
        $this->fileQueue->add('two');
        $this->assertCount(2, $this->fileQueue);
        $this->fileQueue->clear();
        $this->assertCount(0, $this->fileQueue);
    }

    public function testItAddsTheClassNameToTheFileNameForObjectMessages()
    {
        $this->fileQueue->add($this);

        $className = substr(__CLASS__, strrpos(__CLASS__, '\\') + 1);
        $pattern = '*-' . $className;
        $message = sprintf('The message queue did not contain a file matching the pattern /%s', $pattern);

        $this->assertCount(1, glob($this->storagePath . '/' . $pattern), $message);
    }
}
