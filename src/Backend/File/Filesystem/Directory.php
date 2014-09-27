<?php

namespace Brera\Lib\Queue\Backend\File\Filesystem;

class Directory
{
    protected $newDirectoryMode = 0755;

    public function createDirRecursivelyIfNotExists($dirPath)
    {
        if (file_exists($dirPath) && !is_dir($dirPath)) {
            throw new \Exception('Path already exists but is not a directory.');
        }

        if (!file_exists($dirPath)) {
            $oldMask = umask(0);
            mkdir($dirPath, $this->newDirectoryMode, true);
            umask($oldMask);
        }
    }

    /**
     * Get oldest file from directory based on filename convention (time in milliseconds + increment)
     * Note: This approach can be slow for huge directories but there's no other way in PHP to list files alphabetically
     *
     * @param string $dirPath
     * @return string
     */
    public function getNameOfOldestFileInDir($dirPath)
    {
        $files = scandir($dirPath);

        return count($files) > 2 ? $files[2] : '';
    }
}
