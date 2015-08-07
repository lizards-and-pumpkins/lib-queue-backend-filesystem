<?php

namespace Brera\Queue\InMemory;

/**
 * @covers \Brera\Queue\InMemory\InMemoryQueue
 */
class InMemoryQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryQueue
     */
    private $queue;
    
    public function setUp()
    {
        $this->queue = new InMemoryQueue();
    }

    public function testItShouldInitiallyBeEmpty()
    {
        $this->assertCount(0, $this->queue);
    }

    public function testItShouldThrowNotSerializableException()
    {
        $this->setExpectedException(\Brera\Queue\NotSerializableException::class);
        $simpleXml = simplexml_load_string('<root />');
        $this->queue->add($simpleXml);
    }

    public function testItCanAddSerializableObjectToTheQueue()
    {
        $stubSerializableData = $this->getMock(\Serializable::class);
        $this->queue->add($stubSerializableData);
        $this->assertCount(1, $this->queue);
    }

    public function testItIsNotReadyForNextWhenTheQueueIsEmpty()
    {
        $this->assertFalse($this->queue->isReadyForNext());
    }

    public function testItIsReadyForNextWhenTheQueueIsNotEmpty()
    {
        $this->queue->add('dummy');
        $this->assertTrue($this->queue->isReadyForNext());
    }

    public function testItShouldReturnTheNextEventFromTheQueue()
    {
        $stubSerializableData = $this->getMock(\Serializable::class);
        $stubSerializableData->expects($this->once())
            ->method('serialize')
            ->willReturn(serialize(''));
        $this->queue->add($stubSerializableData);
        $result = $this->queue->next();
        $this->assertEquals($stubSerializableData, $result);
    }

    public function testRetrievingTheMessageShouldRemoveItFromTheQueue()
    {
        $stubSerializableData = $this->getMock(\Serializable::class);
        $stubSerializableData->expects($this->once())
                             ->method('serialize')
                             ->willReturn(serialize(''));
        $this->queue->add($stubSerializableData);
        $this->queue->next();
        $this->assertCount(0, $this->queue);
    }
    
    public function testItShouldThrowAnExceptionIfNextIsCalledOnAnEmptyQueue()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->queue->next();
    }
    
    /* TODO: test it should return the events in the correct order */
}
