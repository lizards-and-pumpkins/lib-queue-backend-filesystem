<?php

namespace LizardsAndPumpkins\Queue\File;

class FileNameFixtureFileQueue extends FileQueue
{
    /**
     * @var string
     */
    private $messageFileName;

    /**
     * @param string $storagePath
     * @param string $lockFilePath
     * @param string $messageFileName
     */
    public function __construct($storagePath, $lockFilePath, $messageFileName)
    {
        parent::__construct($storagePath, $lockFilePath);
        $this->messageFileName = $messageFileName;
    }

    /**
     * @return string
     */
    protected function getFileNameForMessage()
    {
        return $this->messageFileName;
    }
}
