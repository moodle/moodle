<?php

declare(strict_types=1);

namespace SAML2\Utilities;

use SAML2\Exception\InvalidArgumentException;
use SAML2\Exception\RuntimeException;

/**
 * Various File Utilities
 */
class File
{
    /**
     * @param string $file full absolute path to the file
     *
     * @return string
     */
    public static function getFileContents(string $file) : string
    {
        if (!is_readable($file)) {
            throw new RuntimeException(sprintf(
                'File "%s" does not exist or is not readable',
                $file
            ));
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException(sprintf(
                'Could not read from existing and readable file "%s"',
                $file
            ));
        }

        return $contents;
    }
}
