<?php

namespace LizardsAndPumpkins\Messaging\Queue\File;

class FileNameFixtureFileQueue extends FileQueue
{
    /**
     * @var string
     */
    private $messageFileName;

    final public function __construct(string $storagePath, string $lockFilePath, string $messageFileName)
    {
        parent::__construct($storagePath, $lockFilePath);
        $this->messageFileName = $messageFileName;
    }

    final protected function getFileNameForMessage($data) : string
    {
        return $this->messageFileName;
    }
}
