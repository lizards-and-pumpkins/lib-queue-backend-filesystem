<?php


namespace Brera\Queue\File;

/**
 * @covers Brera\Queue\File\FileQueue
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
        $this->storagePath = sys_get_temp_dir() . '/brera/test-queue/content';
        $this->lockFilePath = sys_get_temp_dir() . '/brera/test-queue/lock';
        $this->fileQueue = $this->createFileQueueInstance();
    }

    protected function tearDown()
    {
        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
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
        $this->setExpectedException(\Brera\Queue\NotSerializableException::class);
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
}
