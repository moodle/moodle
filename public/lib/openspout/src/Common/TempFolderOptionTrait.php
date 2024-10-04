<?php

declare(strict_types=1);

namespace OpenSpout\Common;

use OpenSpout\Common\Exception\InvalidArgumentException;

/**
 * @internal
 */
trait TempFolderOptionTrait
{
    private string $tempFolder;

    final public function setTempFolder(string $tempFolder): void
    {
        if (!is_dir($tempFolder) || !is_writable($tempFolder)) {
            throw new InvalidArgumentException("{$tempFolder} is not a writable folder");
        }

        $this->tempFolder = $tempFolder;
    }

    final public function getTempFolder(): string
    {
        if (!isset($this->tempFolder)) {
            $this->setTempFolder(sys_get_temp_dir());
        }

        return $this->tempFolder;
    }
}
