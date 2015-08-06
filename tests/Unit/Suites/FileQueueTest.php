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
    
    private $dir;
    private $lockFile;
    
    /**
     * @return FileQueue
     */
    private function createFileQueueInstance()
    {
        return new FileQueue($this->dir, $this->lockFile);
    }

    protected function setUp()
    {
        $this->dir = sys_get_temp_dir() . '/brera/queue/content';
        $this->lockFile = sys_get_temp_dir() . '/brera/queue/lock';
        $this->fileQueue = $this->createFileQueueInstance();
    }

    protected function tearDown()
    {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
        if (file_exists($this->dir)) {
            $list = scandir($this->dir);
            foreach ($list as $fileName) {
                $file = $this->dir . '/' . $fileName;
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->dir);
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
        $nMessages = 1000;
        for ($i = 0; $i < $nMessages; $i++) {
            $this->fileQueue->add($i);
        }
        for ($i = 0; $i < $nMessages; $i++) {
            $this->assertSame($i, $this->fileQueue->next());
        }
    }
}
