<?php

declare(strict_types=1);

namespace Phpml\Exception;

class DatasetException extends \Exception
{
    /**
     * @param string $path
     *
     * @return DatasetException
     */
    public static function missingFolder(string $path)
    {
        return new self(sprintf('Dataset root folder "%s" missing.', $path));
    }
}
