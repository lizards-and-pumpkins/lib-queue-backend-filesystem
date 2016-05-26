<?php

declare(strict_types = 1);

namespace LizardsAndPumpkins\Messaging\Queue\File;

use LizardsAndPumpkins\Messaging\Queue;
use LizardsAndPumpkins\Messaging\Queue\Exception\NotSerializableException;
use LizardsAndPumpkins\Messaging\Queue\Message;
use LizardsAndPumpkins\Util\FileSystem\LocalFilesystem;
use LizardsAndPumpkins\Util\Storage\Clearable;

class FileQueue implements Queue, Clearable
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
    
    public function count(): int
    {
        $this->createStorageDirIfNotExists();
        return count(scandir($this->storagePath)) -2;
    }

    public function isReadyForNext(): bool
    {
        return $this->count() > 0;
    }

    public function add(Message $data)
    {
        $this->createStorageDirIfNotExists();
        $this->retrieveLock();
        $filePath = $this->storagePath . '/' . $this->getFileNameForMessage($data);
        $suffix = $this->getFileNameSuffix($filePath);
        file_put_contents($filePath . $suffix, $data->serialize());
        $this->releaseLock();
    }

    public function next(): Message
    {
        $this->createStorageDirIfNotExists();
        $this->retrieveLock();
        $filePath = $this->getNextFile();
        $data = file_get_contents($filePath);
        unlink($filePath);
        $this->releaseLock();
        return Message::rehydrate($data);
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

    private function getNextFile(): string
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

    protected function getFileNameForMessage(Message $data): string
    {
        return ((string) microtime(true) * 10000) . '-' . $data->getName();
    }

    private function getFileNameSuffix(string $filePath): string
    {
        $suffix = '';
        $count = 0;
        while (file_exists($filePath . $suffix)) {
            $suffix = '_' . ++$count;
        }
        return $suffix;
    }

    public function clear()
    {
        (new LocalFilesystem())->removeDirectoryContents($this->storagePath);
    }
}
