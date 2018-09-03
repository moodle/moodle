<?php

declare(strict_types=1);

namespace Phpml\Exception;

class FileException extends \Exception
{
    /**
     * @param string $filepath
     *
     * @return FileException
     */
    public static function missingFile(string $filepath)
    {
        return new self(sprintf('File "%s" missing.', $filepath));
    }

    /**
     * @param string $filepath
     *
     * @return FileException
     */
    public static function cantOpenFile(string $filepath)
    {
        return new self(sprintf('File "%s" can\'t be open.', $filepath));
    }

    /**
     * @param string $filepath
     *
     * @return FileException
     */
    public static function cantSaveFile(string $filepath)
    {
        return new self(sprintf('File "%s" can\'t be saved.', $filepath));
    }
}
