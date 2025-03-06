<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Wrapper\XMLReader;

use function ltrim;

/**
 * @internal
 */
final class SheetMergeCellsReader
{
    public const XML_NODE_MERGE_CELL = 'mergeCell';
    public const XML_ATTRIBUTE_REF = 'ref';

    /** @var list<string> Merged cells list */
    private array $mergeCells = [];

    /**
     * @param string       $filePath             Path of the XLSX file being read
     * @param string       $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param XMLProcessor $xmlProcessor         Helper to process XML files
     */
    public function __construct(
        string $filePath,
        string $sheetDataXMLFilePath,
        XMLReader $xmlReader,
        XMLProcessor $xmlProcessor
    ) {
        $sheetDataXMLFilePath = ltrim($sheetDataXMLFilePath, '/');

        // Register all callbacks to process different nodes when reading the XML file
        $xmlProcessor->registerCallback(self::XML_NODE_MERGE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processMergeCellsStartingNode']);
        $xmlReader->close();

        if (false === $xmlReader->openFileInZip($filePath, $sheetDataXMLFilePath)) {
            throw new IOException("Could not open \"{$sheetDataXMLFilePath}\".");
        }

        // Now read the entire header of the sheet, until we reach the <sheetData> element
        $xmlProcessor->readUntilStopped();
        $xmlReader->close();
    }

    /**
     * @return list<string>
     */
    public function getMergeCells(): array
    {
        return $this->mergeCells;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<mergeCells>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processMergeCellsStartingNode(XMLReader $xmlReader): int
    {
        $this->mergeCells[] = $xmlReader->getAttribute(self::XML_ATTRIBUTE_REF);

        return XMLProcessor::PROCESSING_CONTINUE;
    }
}
