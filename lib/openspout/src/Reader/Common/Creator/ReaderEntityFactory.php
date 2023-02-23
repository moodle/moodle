<?php

namespace OpenSpout\Reader\Common\Creator;

use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Common\Type;
use OpenSpout\Reader\ReaderInterface;

/**
 * Factory to create external entities.
 */
class ReaderEntityFactory
{
    /**
     * Creates a reader by file extension.
     *
     * @param string $path The path to the spreadsheet file. Supported extensions are .csv, .ods and .xlsx
     *
     * @throws \OpenSpout\Common\Exception\UnsupportedTypeException
     *
     * @return ReaderInterface
     */
    public static function createReaderFromFile(string $path)
    {
        return ReaderFactory::createFromFile($path);
    }

    /**
     * This creates an instance of a CSV reader.
     *
     * @return \OpenSpout\Reader\CSV\Reader
     */
    public static function createCSVReader()
    {
        try {
            return ReaderFactory::createFromType(Type::CSV);
        } catch (UnsupportedTypeException $e) {
            // should never happen
            return null;
        }
    }

    /**
     * This creates an instance of a XLSX reader.
     *
     * @return \OpenSpout\Reader\XLSX\Reader
     */
    public static function createXLSXReader()
    {
        try {
            return ReaderFactory::createFromType(Type::XLSX);
        } catch (UnsupportedTypeException $e) {
            // should never happen
            return null;
        }
    }

    /**
     * This creates an instance of a ODS reader.
     *
     * @return \OpenSpout\Reader\ODS\Reader
     */
    public static function createODSReader()
    {
        try {
            return ReaderFactory::createFromType(Type::ODS);
        } catch (UnsupportedTypeException $e) {
            // should never happen
            return null;
        }
    }
}
