<?php


namespace Brera\Queue\File;

use Brera\Queue\NotSerializableException;
use Brera\Queue\Queue;

class FileQueue implements Queue
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $lockFile;

    /**
     * @var resource
     */
    private $lock;

    /**
     * @param string $dir
     * @param string $lockFile
     */
    public function __construct($dir, $lockFile)
    {
        $this->dir = $dir;
        $this->lockFile = $lockFile;
    }

    public function __destruct()
    {
        $this->releaseLock();
    }
    
    /**
     * @return int
     */
    public function count()
    {
        $this->createDirIfNotExists();
        return count(scandir($this->dir)) -2;
    }

    /**
     * @param mixed $data
     */
    public function add($data)
    {
        $this->getLock();
        $file = $this->dir . '/' . microtime(true);
        file_put_contents($file, $this->serialize($data));
        $this->releaseLock();
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $this->getLock();
        $file = $this->getNextFile();
        $data = unserialize(file_get_contents($file));
        unlink($file);
        $this->releaseLock();
        return $data;
    }

    private function createDirIfNotExists()
    {
        if (!file_exists($this->dir)) {
            mkdir($this->dir, 0777, true);
        }
    }

    private function getLock()
    {
        $this->createDirIfNotExists();
        if (! file_exists($this->lockFile)) {
            touch($this->lockFile);
        }
        $this->lock = fopen($this->lockFile, 'r+');
        flock($this->lock, LOCK_EX);
    }

    private function releaseLock()
    {
        if ($this->lock) {
            flock($this->lock, LOCK_UN);
            fclose($this->lock);
            $this->lock = null;
        }
    }

    /**
     * @return string
     */
    private function getNextFile()
    {
        $files = scandir($this->dir);
        $i = 0;
        while ($i < count($files) && in_array($files[$i], ['.', '..'], true)) {
            $i++;
        }
        if ($i == count($files)) {
            throw new \UnderflowException('Trying to get next message of an empty queue');
        }
        return $this->dir . '/' . $files[$i];
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function serialize($data)
    {
        try {
            return serialize($data);
        } catch (\Exception $e) {
            throw new NotSerializableException($e->getMessage());
        }
    }
}
