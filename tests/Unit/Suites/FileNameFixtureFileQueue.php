<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Messaging\Queue\File;

use LizardsAndPumpkins\Messaging\Queue\Message;

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

    final protected function getFileNameForMessage(Message $data): string
    {
        return $this->messageFileName;
    }
}
