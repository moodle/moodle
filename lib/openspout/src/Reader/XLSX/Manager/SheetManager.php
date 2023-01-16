<?php

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Reader\Common\Entity\Options;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\XLSX\Creator\InternalEntityFactory;
use OpenSpout\Reader\XLSX\Sheet;

/**
 * This class manages XLSX sheets.
 */
class SheetManager
{
    /** Paths of XML files relative to the XLSX file root */
    public const WORKBOOK_XML_RELS_FILE_PATH = 'xl/_rels/workbook.xml.rels';
    public const WORKBOOK_XML_FILE_PATH = 'xl/workbook.xml';

    /** Definition of XML node names used to parse data */
    public const XML_NODE_WORKBOOK_PROPERTIES = 'workbookPr';
    public const XML_NODE_WORKBOOK_VIEW = 'workbookView';
    public const XML_NODE_SHEET = 'sheet';
    public const XML_NODE_SHEETS = 'sheets';
    public const XML_NODE_RELATIONSHIP = 'Relationship';

    /** Definition of XML attributes used to parse data */
    public const XML_ATTRIBUTE_DATE_1904 = 'date1904';
    public const XML_ATTRIBUTE_ACTIVE_TAB = 'activeTab';
    public const XML_ATTRIBUTE_R_ID = 'r:id';
    public const XML_ATTRIBUTE_NAME = 'name';
    public const XML_ATTRIBUTE_STATE = 'state';
    public const XML_ATTRIBUTE_ID = 'Id';
    public const XML_ATTRIBUTE_TARGET = 'Target';

    /** State value to represent a hidden sheet */
    public const SHEET_STATE_HIDDEN = 'hidden';

    /** @var string Path of the XLSX file being read */
    protected $filePath;

    /** @var \OpenSpout\Common\Manager\OptionsManagerInterface Reader's options manager */
    protected $optionsManager;

    /** @var \OpenSpout\Reader\XLSX\Manager\SharedStringsManager Manages shared strings */
    protected $sharedStringsManager;

    /** @var \OpenSpout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /** @var InternalEntityFactory Factory to create entities */
    protected $entityFactory;

    /** @var \OpenSpout\Common\Helper\Escaper\XLSX Used to unescape XML data */
    protected $escaper;

    /** @var array List of sheets */
    protected $sheets;

    /** @var int Index of the sheet currently read */
    protected $currentSheetIndex;

    /** @var int Index of the active sheet (0 by default) */
    protected $activeSheetIndex;

    /**
     * @param string                                              $filePath             Path of the XLSX file being read
     * @param \OpenSpout\Common\Manager\OptionsManagerInterface   $optionsManager       Reader's options manager
     * @param \OpenSpout\Reader\XLSX\Manager\SharedStringsManager $sharedStringsManager Manages shared strings
     * @param \OpenSpout\Common\Helper\Escaper\XLSX               $escaper              Used to unescape XML data
     * @param InternalEntityFactory                               $entityFactory        Factory to create entities
     * @param mixed                                               $sharedStringsManager
     */
    public function __construct($filePath, $optionsManager, $sharedStringsManager, $escaper, $entityFactory)
    {
        $this->filePath = $filePath;
        $this->optionsManager = $optionsManager;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->escaper = $escaper;
        $this->entityFactory = $entityFactory;
    }

    /**
     * Returns the sheets metadata of the file located at the previously given file path.
     * The paths to the sheets' data are read from the [Content_Types].xml file.
     *
     * @return Sheet[] Sheets within the XLSX file
     */
    public function getSheets()
    {
        $this->sheets = [];
        $this->currentSheetIndex = 0;
        $this->activeSheetIndex = 0; // By default, the first sheet is active

        $xmlReader = $this->entityFactory->createXMLReader();
        $xmlProcessor = $this->entityFactory->createXMLProcessor($xmlReader);

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
     * @param \OpenSpout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<workbookPr>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processWorkbookPropertiesStartingNode($xmlReader)
    {
        // Using "filter_var($x, FILTER_VALIDATE_BOOLEAN)" here because the value of the "date1904" attribute
        // may be the string "false", that is not mapped to the boolean "false" by default...
        $shouldUse1904Dates = filter_var($xmlReader->getAttribute(self::XML_ATTRIBUTE_DATE_1904), FILTER_VALIDATE_BOOLEAN);
        $this->optionsManager->setOption(Options::SHOULD_USE_1904_DATES, $shouldUse1904Dates);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param \OpenSpout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<workbookView>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processWorkbookViewStartingNode($xmlReader)
    {
        // The "workbookView" node is located before "sheet" nodes, ensuring that
        // the active sheet is known before parsing sheets data.
        $this->activeSheetIndex = (int) $xmlReader->getAttribute(self::XML_ATTRIBUTE_ACTIVE_TAB);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @param \OpenSpout\Reader\Wrapper\XMLReader $xmlReader XMLReader object, positioned on a "<sheet>" starting node
     *
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processSheetStartingNode($xmlReader)
    {
        $isSheetActive = ($this->currentSheetIndex === $this->activeSheetIndex);
        $this->sheets[] = $this->getSheetFromSheetXMLNode($xmlReader, $this->currentSheetIndex, $isSheetActive);
        ++$this->currentSheetIndex;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    /**
     * @return int A return code that indicates what action should the processor take next
     */
    protected function processSheetsEndingNode()
    {
        return XMLProcessor::PROCESSING_STOP;
    }

    /**
     * Returns an instance of a sheet, given the XML node describing the sheet - from "workbook.xml".
     * We can find the XML file path describing the sheet inside "workbook.xml.res", by mapping with the sheet ID
     * ("r:id" in "workbook.xml", "Id" in "workbook.xml.res").
     *
     * @param \OpenSpout\Reader\Wrapper\XMLReader $xmlReaderOnSheetNode XML Reader instance, pointing on the node describing the sheet, as defined in "workbook.xml"
     * @param int                                 $sheetIndexZeroBased  Index of the sheet, based on order of appearance in the workbook (zero-based)
     * @param bool                                $isSheetActive        Whether this sheet was defined as active
     *
     * @return \OpenSpout\Reader\XLSX\Sheet Sheet instance
     */
    protected function getSheetFromSheetXMLNode($xmlReaderOnSheetNode, $sheetIndexZeroBased, $isSheetActive)
    {
        $sheetId = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_R_ID);

        $sheetState = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_STATE);
        $isSheetVisible = (self::SHEET_STATE_HIDDEN !== $sheetState);

        $escapedSheetName = $xmlReaderOnSheetNode->getAttribute(self::XML_ATTRIBUTE_NAME);
        $sheetName = $this->escaper->unescape($escapedSheetName);

        $sheetDataXMLFilePath = $this->getSheetDataXMLFilePathForSheetId($sheetId);

        return $this->entityFactory->createSheet(
            $this->filePath,
            $sheetDataXMLFilePath,
            $sheetIndexZeroBased,
            $sheetName,
            $isSheetActive,
            $isSheetVisible,
            $this->optionsManager,
            $this->sharedStringsManager
        );
    }

    /**
     * @param string $sheetId The sheet ID, as defined in "workbook.xml"
     *
     * @return string The XML file path describing the sheet inside "workbook.xml.res", for the given sheet ID
     */
    protected function getSheetDataXMLFilePathForSheetId($sheetId)
    {
        $sheetDataXMLFilePath = '';

        // find the file path of the sheet, by looking at the "workbook.xml.res" file
        $xmlReader = $this->entityFactory->createXMLReader();
        if ($xmlReader->openFileInZip($this->filePath, self::WORKBOOK_XML_RELS_FILE_PATH)) {
            while ($xmlReader->read()) {
                if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_RELATIONSHIP)) {
                    $relationshipSheetId = $xmlReader->getAttribute(self::XML_ATTRIBUTE_ID);

                    if ($relationshipSheetId === $sheetId) {
                        // In workbook.xml.rels, it is only "worksheets/sheet1.xml"
                        // In [Content_Types].xml, the path is "/xl/worksheets/sheet1.xml"
                        $sheetDataXMLFilePath = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TARGET);

                        // sometimes, the sheet data file path already contains "/xl/"...
                        if (0 !== strpos($sheetDataXMLFilePath, '/xl/')) {
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
}
