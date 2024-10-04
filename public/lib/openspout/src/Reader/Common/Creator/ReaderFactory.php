<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Common\Creator;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\ODS\Reader as ODSReader;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;

/**
 * This factory is used to create readers, based on the type of the file to be read.
 * It supports CSV, XLSX and ODS formats.
 *
 * @deprecated Guessing mechanisms are brittle by nature and won't be provided by this library anymore
 */
final class ReaderFactory
{
    /**
     * Creates a reader by file extension.
     *
     * @param string $path The path to the spreadsheet file. Supported extensions are .csv,.ods and .xlsx
     *
     * @throws UnsupportedTypeException
     */
    public static function createFromFile(string $path): ReaderInterface
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'csv' => new CSVReader(),
            'xlsx' => new XLSXReader(),
            'ods' => new ODSReader(),
            default => throw new UnsupportedTypeException('No readers supporting the given type: '.$extension),
        };
    }

    /**
     * Creates a reader by mime type.
     *
     * @param string $path the path to the spreadsheet file
     *
     * @throws UnsupportedTypeException
     * @throws IOException
     */
    public static function createFromFileByMimeType(string $path): ReaderInterface
    {
        if (!file_exists($path)) {
            throw new IOException("Could not open {$path} for reading! File does not exist.");
        }

        $mime_type = mime_content_type($path);

        return match ($mime_type) {
            'application/csv', 'text/csv', 'text/plain' => new CSVReader(),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => new XLSXReader(),
            'application/vnd.oasis.opendocument.spreadsheet' => new ODSReader(),
            default => throw new UnsupportedTypeException('No readers supporting the given type: '.$mime_type),
        };
    }
}
