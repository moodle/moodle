<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Reader\Common\Manager\RowManager;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Wrapper\XMLReader;
use OpenSpout\Reader\XLSX\Helper\CellValueFormatter;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\RowIterator;
use OpenSpout\Reader\XLSX\Sheet;
use OpenSpout\Reader\XLSX\SheetHeaderReader;
use OpenSpout\Reader\XLSX\SheetMergeCellsReader;

/**
 * @internal
 */
final class SheetManager
{
    /**
     * Paths of XML files relative to the XLSX file root.
     */
    public const WORKBOOK_XML_RELS_FILE_PATH = 'xl/_rels/workbook.xml.rels';
    public const WORKBOOK_XML_FILE_PATH = 'xl/workbook.xml';

    /**
     * Definition of XML node names used to parse data.
     */
    public const XML_NODE_WORKBOOK_PROPERTIES = 'workbookPr';
    public const XML_NODE_WORKBOOK_VIEW = 'workbookView';
    public const XML_NODE_SHEET = 'sheet';
    public const XML_NODE_SHEETS = 'sheets';
    public const XML_NODE_RELATIONSHIP = 'Relationship';

    /**
     * Definition of XML attributes used to parse data.
     */
    public const XML_ATTRIBUTE_DATE_1904 = 'date1904';
    public const XML_ATTRIBUTE_ACTIVE_TAB = 'activeTab';
    public const XML_ATTRIBUTE_R_ID = 'r:id';
    public const XML_ATTRIBUTE_NAME = 'name';
    public const XML_ATTRIBUTE_STATE = 'state';
    public const XML_ATTRIBUTE_ID = 'Id';
    public const XML_ATTRIBUTE_TARGET = 'Target';

    /**
     * State value to represent a hidden sheet.
     */
    public const SHEET_STATE_HIDDEN = 'hidden';
    public const SHEET_STATE_VERY_HIDDEN = 'veryHidden';

    /** @var string Path of the XLSX file being read */
    private readonly string $filePath;

    private readonly Options $options;

    /** @var SharedStringsManager Manages shared strings */
    private readonly SharedStringsManager $sharedStringsManager;

    /** @var XLSX Used to unescape XML data */
    private readonly XLSX $escaper;

    /** @var Sheet[] List of sheets */
    private array $sheets;

    /** @var int Index of the sheet currently read */
    private int $currentSheetIndex;

    /** @var int Index of the active sheet (0 by default) */
    private int $activeSheetIndex;

    public function __construct(
        string $filePath,
        Options $options,
        SharedStringsManager $sharedStringsManager,
        XLSX $escaper
    ) {
        $this->filePath = $filePath;
        $this->options = $options;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->escaper = $escaper;
    }

    /**
     * Returns the sheets metadata of the file located at the previously given file path.
     * The paths to the sheets' data are read from the [Content_Types].xml file.
     *
     * @return Sheet[] Sheets within the XLSX file
     */
    public function getSheets(): array
    {
        $this->sheets = [];
        $this->currentSheetIndex = 0;
        $this->activeSheetIndex = 0; // By default, the first sheet is active

        $xmlReader = new XMLReader();
        $xmlProcessor = new XMLProcessor($xmlReader);

        $xmlProcessor->registerCallback(self::XML_NODE_WORKBOOK_PROPERTIES, XMLProcessor::NODE_TYPE_START, [$this, 'processWorkbookPropertiesStartingNode']);
        $xmlProcessor->registerCallback(self::XML_NODE_WORKBOOK_VIEW, XMLProcessor::NODE_TYPE_START, [$this, 'processWorkbookViewStartingNode']);
        $xmlProcessor->registerCallback(self::XML_NODE_SHEET, XMLProcessor::NODE_TYPE_START, [$this, 'processSheetStartingNode']);
        $xmlProcessor->registerCallback(self::XML_NODE_SHEETS, XMLProcessor::NODE_TYPE_END, [$this, 'processSheetsEndingNode']);

        if ($xmlReader->openFileInZip($this->filePath, self::WORKBOOK_XML_FILE_PATH)) {
            $xmlProcessor->readUntilStopped();
            $xmlReader->close();
        }

        return $this->sheets;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<workbookPr>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processWorkbookPropertiesStartingNode(XMLReader $xmlReader): int
    {
        // Using "filter_var($x, FILTER_VALIDATE_BOOLEAN)" here because the value of the "date1904" attribute
        // may be the string "false", that is not mapped to the boolean "false" by default...
        $shouldUse1904Dates = filter_var($xmlReader->getAttribute(self::XML_ATTRIBUTE_DATE_1904), FILTER_VALIDATE_BOOLEAN);
        $this->options->SHOULD_USE_1904_DATES = $shouldUse1904Dates;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<workbookView>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processWorkbookViewStartingNode(XMLReader $xmlReader): int
    {
        // The "workbookView" node is located before "sheet" nodes, ensuring that
        // the active sheet is known before parsing sheets data.
        $this->activeSheetIndex = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_ACTIVE_TAB);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param XMLReader $xmlReader XMLReader object, positioned on a "<sheet>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    private function processSheetStartingNode(XMLReader $xmlReader): int
    {
        $isSheetActive = ($this->currentSheetIndex === $this->activeSheetIndex);
        $this->sheets[] = $this->getSheetFromSheetXMLNode($xmlReader, $this->currentSheetIndex, $isSheetActive);
        ++$this->currentSheetIndex;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    private function processSheetsEndingNode(): int
    {
        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * Returns an instance of a sheet, given the XML node describing the sheet - from "workbook.xml".
     * We can find the XML file path describing the sheet inside "workbook.xml.res", by mapping with the sheet ID
     * ("r:id" in "workbook.xml", "Id" in "workbook.xml.res").
     *
     * @param XMLReader $xmlReaderOnSheetNode XML Reader instance, pointing on the node describing the sheet, as defined in "workbook.xml"
     * @param int       $sheetIndexZeroBased  Index of the sheet, based on order of appearance in the workbook (zero-based)
     * @param bool      $isSheetActive        Whether this sheet was defined as active
     *
     * @return Sheet Sheet instance
     */
    private function getSheetFromSheetXMLNode(XMLReader $xmlReaderOnSheetNode, int $sheetIndexZeroBased, bool $isSheetActive): Sheet
    {
        $sheetId = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_R_ID);
        \assert(null !== $sheetId);

        $sheetState = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_STATE);
        $isSheetVisible = (self::SHEET_STATE_HIDDEN !== $sheetState && self::SHEET_STATE_VERY_HIDDEN !== $sheetState);

        $escapedSheetName = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_NAME);
        \assert(null !== $escapedSheetName);
        $sheetName = $this->escaper->unescape($escapedSheetName);

        $sheetDataXMLFilePath = $this->getSheetDataXMLFilePathForSheetId($sheetId);

        $mergeCells = [];
        if ($this->options->SHOULD_LOAD_MERGE_CELLS) {
            $mergeCells = (new SheetMergeCellsReader(
                $this->filePath,
                $sheetDataXMLFilePath,
                $xmlReader = new XMLReader(),
                new XMLProcessor($xmlReader)
            ))->getMergeCells();
        }

        return new Sheet(
            $this->createRowIterator($this->filePath, $sheetDataXMLFilePath, $this->options, $this->sharedStringsManager),
            $this->createSheetHeaderReader($this->filePath, $sheetDataXMLFilePath),
            $sheetIndexZeroBased,
            $sheetName,
            $isSheetActive,
            $isSheetVisible,
            $mergeCells
        );
    }

    /**
     * @param string $sheetId The sheet ID, as defined in "workbook.xml"
     *
     * @return string The XML file path describing the sheet inside "workbook.xml.res", for the given sheet ID
     */
    private function getSheetDataXMLFilePathForSheetId(string $sheetId): string
    {
        $sheetDataXMLFilePath = '';

        // find the file path of the sheet, by looking at the "workbook.xml.res" file
        $xmlReader = new XMLReader();
        if ($xmlReader->openFileInZip($this->filePath, self::WORKBOOK_XML_RELS_FILE_PATH)) {
            while ($xmlReader->read()) {
                if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_RELATIONSHIP)) {
                    $relationshipSheetId = $xmlReader->getAttribute(self::XML_ATTRIBUTE_ID);

                    if ($relationshipSheetId === $sheetId) {
                        // In workbook.xml.rels, it is only "worksheets/sheet1.xml"
                        // In [Content_Types].xml, the path is "/xl/worksheets/sheet1.xml"
                        $sheetDataXMLFilePath = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TARGET);
                        \assert(null !== $sheetDataXMLFilePath);

                        // sometimes, the sheet data file path already contains "/xl/"...
                        if (!str_starts_with($sheetDataXMLFilePath, '/xl/')) {
                            $sheetDataXMLFilePath = '/xl/'.$sheetDataXMLFilePath;

                            break;
                        }
                    }
                }
            }

            $xmlReader->close();
        }

        return $sheetDataXMLFilePath;
    }

    private function createRowIterator(
        string $filePath,
        string $sheetDataXMLFilePath,
        Options $options,
        SharedStringsManager $sharedStringsManager
    ): RowIterator {
        $workbookRelationshipsManager = new WorkbookRelationshipsManager($filePath);
        $styleManager = new StyleManager(
            $filePath,
            $workbookRelationshipsManager->hasStylesXMLFile()
                ? $workbookRelationshipsManager->getStylesXMLFilePath()
                : null
        );

        $cellValueFormatter = new CellValueFormatter(
            $sharedStringsManager,
            $styleManager,
            $options->SHOULD_FORMAT_DATES,
            $options->SHOULD_USE_1904_DATES,
            new XLSX()
        );

        return new RowIterator(
            $filePath,
            $sheetDataXMLFilePath,
            $options->SHOULD_PRESERVE_EMPTY_ROWS,
            $xmlReader = new XMLReader(),
            new XMLProcessor($xmlReader),
            $cellValueFormatter,
            new RowManager()
        );
    }

    private function createSheetHeaderReader(
        string $filePath,
        string $sheetDataXMLFilePath
    ): SheetHeaderReader {
        $xmlReader = new XMLReader();

        return new SheetHeaderReader(
            $filePath,
            $sheetDataXMLFilePath,
            $xmlReader,
            new XMLProcessor($xmlReader)
        );
    }
}
