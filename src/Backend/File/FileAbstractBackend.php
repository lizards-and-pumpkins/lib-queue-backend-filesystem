<?php

namespace Brera\Lib\Queue\Backend\File;

use Brera\Lib\Queue\Backend\File\Filesystem\Directory,
    Brera\Lib\Queue\Backend\File\Filesystem\File;

abstract class FileAbstractBackend
{
    const STATE_PENDING = 'pending';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETED = 'completed';

    /**
     * @var FileConfig
     */
    protected $config;

    /**
     * @var Directory
     */
    protected $directory;

    /**
     * @var File
     */
    protected $file;

    protected $initializedChannels = array();

    protected $lockFileHandle;

    public function checkIfChannelIsInitialized($channelName)
    {
        if (!in_array($channelName, $this->initializedChannels)) {
            $this->checkConfig();

            $pendingStateDir = $this->getMessageStateDir($channelName, self::STATE_PENDING);
            $this->directory->createDirRecursivelyIfNotExists($pendingStateDir);

            $processingStateDir = $this->getMessageStateDir($channelName, self::STATE_PROCESSING);
            $this->directory->createDirRecursivelyIfNotExists($processingStateDir);

            $completedStateDir = $this->getMessageStateDir($channelName, self::STATE_COMPLETED);
            $this->directory->createDirRecursivelyIfNotExists($completedStateDir);

            array_push($this->initializedChannels, $channelName);
        }

        if (!is_resource($this->lockFileHandle)) {
            $channelPath = $this->config->getStorageRootDir() . DIRECTORY_SEPARATOR . $channelName;
            $this->lockFileHandle = $this->file->getNewFileHandle($channelPath . DIRECTORY_SEPARATOR . 'lock', 'w+');
        }
    }

    public function changeMessageState($channelName, $filePath, $newState)
    {
        $newPath = $this->getMessageStateDir($channelName, $newState);
        $this->file->moveFile($filePath, $newPath);
    }

    protected function getMessageStateDir($channelName, $messageState)
    {
        $channelDir = $this->config->getStorageRootDir() . DIRECTORY_SEPARATOR . $channelName;
        $messageStateDir = $channelDir . DIRECTORY_SEPARATOR . $messageState;

        return $messageStateDir;
    }

    protected function checkConfig()
    {
        if (!$this->config->getStorageRootDir()) {
            throw new \Exception('Root storage path is not set.');
        }
    }
}
