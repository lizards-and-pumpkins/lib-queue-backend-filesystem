<?php


namespace Brera\Queue\File;

use Brera\Queue\NotSerializableException;
use Brera\Queue\Queue;

class FileQueue implements Queue
{
    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var string
     */
    private $lockFilePath;

    /**
     * @var resource
     */
    private $lock;

    /**
     * @param string $storagePath
     * @param string $lockFilePath
     */
    public function __construct($storagePath, $lockFilePath)
    {
        $this->storagePath = $storagePath;
        $this->lockFilePath = $lockFilePath;
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
        $this->createStorageDirIfNotExists();
        return count(scandir($this->storagePath)) -2;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function add($data)
    {
        $this->createStorageDirIfNotExists();
        $this->retrieveLock();
        $file = $this->storagePath . '/' . microtime(true);
        file_put_contents($file, $this->serialize($data));
        $this->releaseLock();
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $this->createStorageDirIfNotExists();
        $this->retrieveLock();
        $file = $this->getNextFile();
        $data = unserialize(file_get_contents($file));
        unlink($file);
        $this->releaseLock();
        return $data;
    }

    private function createStorageDirIfNotExists()
    {
        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    private function createLockFileIfNotExists()
    {
        if (!file_exists($this->lockFilePath)) {
            $this->createLockFileDir();
            touch($this->lockFilePath);
        }
    }

    private function createLockFileDir()
    {
        $lockFileDir = dirname($this->lockFilePath);
        if (!file_exists($lockFileDir)) {
            mkdir($lockFileDir, 0777, true);
        }
    }

    private function retrieveLock()
    {
        $this->createLockFileIfNotExists();
        $this->lock = fopen($this->lockFilePath, 'r+');
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
        $files = scandir($this->storagePath);
        $i = 0;
        while ($i < count($files) && in_array($files[$i], ['.', '..'], true)) {
            $i++;
        }
        if ($i == count($files)) {
            throw new \UnderflowException('Trying to get next message of an empty queue');
        }
        return $this->storagePath . '/' . $files[$i];
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
