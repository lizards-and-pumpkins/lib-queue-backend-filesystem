<?php

namespace LizardsAndPumpkins\Messaging\Queue\File;

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
    final public function __construct($storagePath, $lockFilePath, $messageFileName)
    {
        parent::__construct($storagePath, $lockFilePath);
        $this->messageFileName = $messageFileName;
    }

    /**
     * @param string $data
     * @return string
     */
    final protected function getFileNameForMessage($data)
    {
        return $this->messageFileName;
    }
}
