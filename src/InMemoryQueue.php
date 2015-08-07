<?php

namespace Brera\Queue\InMemory;

use Brera\Queue\Queue;
use Brera\Queue\NotSerializableException;

class InMemoryQueue implements Queue
{
    /**
     * @var array
     */
    private $queue = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->queue);
    }

    /**
     * @return bool
     */
    public function isReadyForNext()
    {
        return $this->count() > 0;
    }

    /**
     * @param mixed $data
     * @throws NotSerializableException
     * @return null
     */
    public function add($data)
    {
        try {
            $this->queue[] = serialize($data);
        } catch (\Exception $e) {
            throw new NotSerializableException($e->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws \UnderflowException
     */
    public function next()
    {
        if (empty($this->queue)) {
            throw new \UnderflowException('Trying to get next message of an empty queue');
        }

        $data = array_shift($this->queue);

        return unserialize($data);
    }
}
