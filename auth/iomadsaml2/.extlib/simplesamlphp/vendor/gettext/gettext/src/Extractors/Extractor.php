<?php

namespace Gettext\Extractors;

use Exception;
use InvalidArgumentException;
use Gettext\Translations;

abstract class Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            $options['file'] = $file;
            static::fromString(static::readFile($file), $translations, $options);
        }
    }

    /**
     * Checks and returns all files.
     *
     * @param string|array $file The file/s
     *
     * @return array The file paths
     */
    protected static function getFiles($file)
    {
        if (empty($file)) {
            throw new InvalidArgumentException('There is not any file defined');
        }

        if (is_string($file)) {
            if (!is_file($file)) {
                throw new InvalidArgumentException("'$file' is not a valid file");
            }

            if (!is_readable($file)) {
                throw new InvalidArgumentException("'$file' is not a readable file");
            }

            return [$file];
        }

        if (is_array($file)) {
            $files = [];

            foreach ($file as $f) {
                $files = array_merge($files, static::getFiles($f));
            }

            return $files;
        }

        throw new InvalidArgumentException('The first argument must be string or array');
    }

    /**
     * Reads and returns the content of a file.
     *
     * @param string $file
     *
     * @return string
     */
    protected static function readFile($file)
    {
        $length = filesize($file);

        if (!($fd = fopen($file, 'rb'))) {
            throw new Exception("Cannot read the file '$file', probably permissions");
        }

        $content = $length ? fread($fd, $length) : '';
        fclose($fd);

        return $content;
    }
}
