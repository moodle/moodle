<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Common\ColumnWidth;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Wrapper\XMLReader;

final class SheetHeaderReader
{
    public const XML_NODE_COL = 'col';
    public const XML_NODE_SHEETDATA = 'sheetData';
    public const XML_ATTRIBUTE_MIN = 'min';
    public const XML_ATTRIBUTE_MAX = 'max';
    public const XML_ATTRIBUTE_WIDTH = 'width';

    /** @var string Path of the XLSX file being read */
    private string $filePath;

    /** @var string Path of the sheet data XML file as in [Content_Types].xml */
    private string $sheetDataXMLFilePath;

    /** @var XMLReader The XMLReader object that will help read sheet's XML data */
    private XMLReader $xmlReader;

    /** @var XMLProcessor Helper Object to process XML nodes */
    private XMLProcessor $xmlProcessor;

    /** @var ColumnWidth[] The widths of the columns in the sheet, if specified */
    private array $columnWidths = [];

    /**
     * @param string       $filePath             Path of the XLSX file being read
     * @param string       $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @param XMLReader    $xmlReader            XML Reader
     * @param XMLProcessor $xmlProcessor         Helper to process XML files
     */
    public function __construct(
        string $filePath,
        string $sheetDataXMLFilePath,
        XMLReader $xmlReader,
        XMLProcessor $xmlProcessor
    ) {
        $this->filePath = $filePath;
        $this->sheetDataXMLFilePath = $this->normalizeSheetDataXMLFilePath($sheetDataXMLFilePath);
        $this->xmlReader = $xmlReader;

        // Register all callbacks to process different nodes when reading the XML file
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_COL, XMLProcessor::NODE_TYPE_START, [$this, 'processColStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_SHEETDATA, XMLProcessor::NODE_TYPE_START, [$this, 'processSheetDataStartingNode']);

        // The reader should be unused, but we close to be sure
        $this->xmlReader->close();

        if (false === $this->xmlReader->openFileInZip($this->filePath, $this->sheetDataXMLFilePath)) {
            throw new IOException("Could not open \"{$this->sheetDataXMLFilePath}\".");
        }

        // Now read the entire header of the sheet, until we reach the <sheetData> element
        $this->xmlProcessor->readUntilStopped();

        // We don't need the reader anymore, so we close it
        $this->xmlReader->close();
    }

    /**
     * @internal
     *
     * @return ColumnWidth[]
     */
    public function getColumnWidths(): array
    {
        return $this->columnWidths;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<col>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processColStartingNode(XMLReader $xmlReader): int
    {
        $min = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_MIN);
        $max = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_MAX);
        $width = (float) $xmlReader->getAttribute(self::XML_ATTRIBUTE_WIDTH);

        \assert($min > 0);
        \assert($max > 0);

        $columnwidth = new ColumnWidth($min, $max, $width);
        $this->columnWidths[] = $columnwidth;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    private function processSheetDataStartingNode(): int
    {
        // The opening "<sheetData>" marks the end of the file
        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     *
     * @return string path of the XML file containing the sheet data,
     *                without the leading slash
     */
    private function normalizeSheetDataXMLFilePath(string $sheetDataXMLFilePath): string
    {
        return ltrim($sheetDataXMLFilePath, '/');
    }
}
