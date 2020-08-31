<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Messaging\Queue\File;

use LizardsAndPumpkins\Messaging\Queue\File\Exception\MessageCanNotBeStoredException;
use LizardsAndPumpkins\Messaging\Queue\Message;
use LizardsAndPumpkins\Messaging\Queue\MessageReceiver;
use LizardsAndPumpkins\Messaging\Queue\Queue;
use LizardsAndPumpkins\Util\FileSystem\Exception\DirectoryDoesNotExistException;
use LizardsAndPumpkins\Util\FileSystem\LocalFilesystem;

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

    public function __construct(string $storagePath, string $lockFilePath)
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

        return count(scandir($this->storagePath)) - 2;
    }

    public function add(Message $data): void
    {
        $this->createStorageDirIfNotExists();
        $this->retrieveLock();
        $filePath = $this->storagePath . '/' . $this->getFileNameForMessage($data);
        $suffix = $this->getFileNameSuffix($filePath);
        $result = file_put_contents($filePath . $suffix, $data->serialize());
        $this->releaseLock();

        if (false === $result) {
            throw new MessageCanNotBeStoredException(
                'Message can not be written into a queue. Disk full? Permissions are wrong?'
            );
        }
    }

    public function consume(MessageReceiver $messageReceiver, int $numberOfMessagesToConsume): void
    {
        while ($numberOfMessagesToConsume > 0) {
            $this->retrieveLock();
            if ($this->isReadyForNext()) {
                $message = $this->next();
                $this->releaseLock();
                $messageReceiver->receive($message);
                $numberOfMessagesToConsume--;
            } else {
                $this->releaseLock();
                usleep(250000);
            }
        }
    }

    private function next(): Message
    {
        $filePath = $this->getNextFile();
        $data = file_get_contents($filePath);
        unlink($filePath);

        return Message::rehydrate($data);
    }

    private function isReadyForNext(): bool
    {
        return $this->count() > 0;
    }

    private function createStorageDirIfNotExists(): void
    {
        $this->createDirectory($this->storagePath);
    }

    private function createLockFileIfNotExists(): void
    {
        if (! file_exists($this->lockFilePath)) {
            $this->createLockFileDir();
            touch($this->lockFilePath);
        }
    }

    private function createLockFileDir(): void
    {
        $directory = dirname($this->lockFilePath);
        $this->createDirectory($directory);
    }

    private function retrieveLock(): void
    {
        $this->createLockFileIfNotExists();
        $this->lock = fopen($this->lockFilePath, 'r+');
        flock($this->lock, LOCK_EX);
    }

    private function releaseLock(): void
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
            $i ++;
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
            $suffix = '_' . ++ $count;
        }

        return $suffix;
    }

    public function clear()
    {
        (new LocalFilesystem())->removeDirectoryContents($this->storagePath);
    }

    /**
     * @param $directory
     */
    private function createDirectory($directory)
    {
        if (is_dir($directory)) {
            return;
        }
        if (@mkdir($directory, 0777, true) && ! is_dir($directory)) {
            throw new DirectoryDoesNotExistException(sprintf('Directory %s could not be created.', $directory));
        }
    }
}
