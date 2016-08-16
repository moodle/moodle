<?php

namespace Box\Spout\Reader\XLSX\Helper;

use Box\Spout\Reader\Wrapper\SimpleXMLElement;
use Box\Spout\Reader\XLSX\Sheet;

/**
 * Class SheetHelper
 * This class provides helper functions related to XLSX sheets
 *
 * @package Box\Spout\Reader\XLSX\Helper
 */
class SheetHelper
{
    /** Paths of XML files relative to the XLSX file root */
    const CONTENT_TYPES_XML_FILE_PATH = '[Content_Types].xml';
    const WORKBOOK_XML_RELS_FILE_PATH = 'xl/_rels/workbook.xml.rels';
    const WORKBOOK_XML_FILE_PATH = 'xl/workbook.xml';

    /** Namespaces for the XML files */
    const MAIN_NAMESPACE_FOR_CONTENT_TYPES_XML = 'http://schemas.openxmlformats.org/package/2006/content-types';
    const MAIN_NAMESPACE_FOR_WORKBOOK_XML_RELS = 'http://schemas.openxmlformats.org/package/2006/relationships';
    const MAIN_NAMESPACE_FOR_WORKBOOK_XML = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    /** Value of the Override attribute used in [Content_Types].xml to define sheets */
    const OVERRIDE_CONTENT_TYPES_ATTRIBUTE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml';

    /** @var string Path of the XLSX file being read */
    protected $filePath;

    /** @var \Box\Spout\Reader\XLSX\Helper\SharedStringsHelper Helper to work with shared strings */
    protected $sharedStringsHelper;

    /** @var \Box\Spout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /** @var \Box\Spout\Reader\Wrapper\SimpleXMLElement XML element representing the workbook.xml.rels file */
    protected $workbookXMLRelsAsXMLElement;

    /** @var \Box\Spout\Reader\Wrapper\SimpleXMLElement XML element representing the workbook.xml file */
    protected $workbookXMLAsXMLElement;

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param \Box\Spout\Reader\XLSX\Helper\SharedStringsHelper Helper to work with shared strings
     * @param \Box\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     */
    public function __construct($filePath, $sharedStringsHelper, $globalFunctionsHelper)
    {
        $this->filePath = $filePath;
        $this->sharedStringsHelper = $sharedStringsHelper;
        $this->globalFunctionsHelper = $globalFunctionsHelper;
    }

    /**
     * Returns the sheets metadata of the file located at the previously given file path.
     * The paths to the sheets' data are read from the [Content_Types].xml file.
     *
     * @return Sheet[] Sheets within the XLSX file
     */
    public function getSheets()
    {
        $sheets = [];

        $contentTypesAsXMLElement = $this->getFileAsXMLElementWithNamespace(
            self::CONTENT_TYPES_XML_FILE_PATH,
            self::MAIN_NAMESPACE_FOR_CONTENT_TYPES_XML
        );

        // find all nodes defining a sheet
        $sheetNodes = $contentTypesAsXMLElement->xpath('//ns:Override[@ContentType="' . self::OVERRIDE_CONTENT_TYPES_ATTRIBUTE . '"]');
        $numSheetNodes = count($sheetNodes);

        for ($i = 0; $i < $numSheetNodes; $i++) {
            $sheetNode = $sheetNodes[$i];
            $sheetDataXMLFilePath = $sheetNode->getAttribute('PartName');

            $sheets[] = $this->getSheetFromXML($sheetDataXMLFilePath);
        }

        // make sure the sheets are sorted by index
        // (as the sheets are not necessarily in this order in the XML file)
        usort($sheets, function ($sheet1, $sheet2) {
            return ($sheet1->getIndex() - $sheet2->getIndex());
        });

        return $sheets;
    }

    /**
     * Returns an instance of a sheet, given the path of its data XML file.
     * We first look at "xl/_rels/workbook.xml.rels" to find the relationship ID of the sheet.
     * Then we look at "xl/worbook.xml" to find the sheet entry associated to the found ID.
     * The entry contains the ID and name of the sheet.
     *
     * @param string $sheetDataXMLFilePath Path of the sheet data XML file as in [Content_Types].xml
     * @return \Box\Spout\Reader\XLSX\Sheet Sheet instance
     */
    protected function getSheetFromXML($sheetDataXMLFilePath)
    {
        // In [Content_Types].xml, the path is "/xl/worksheets/sheet1.xml"
        // In workbook.xml.rels, it is only "worksheets/sheet1.xml"
        $sheetDataXMLFilePathInWorkbookXMLRels = ltrim($sheetDataXMLFilePath, '/xl/');

        // find the node associated to the given file path
        $workbookXMLResElement = $this->getWorkbookXMLRelsAsXMLElement();
        $relationshipNodes = $workbookXMLResElement->xpath('//ns:Relationship[@Target="' . $sheetDataXMLFilePathInWorkbookXMLRels . '"]');
        $relationshipNode = $relationshipNodes[0];

        $relationshipSheetId = $relationshipNode->getAttribute('Id');

        $workbookXMLElement = $this->getWorkbookXMLAsXMLElement();
        $sheetNodes = $workbookXMLElement->xpath('//ns:sheet[@r:id="' . $relationshipSheetId . '"]');
        $sheetNode = $sheetNodes[0];

        $escapedSheetName = $sheetNode->getAttribute('name');
        $sheetIdOneBased = $sheetNode->getAttribute('sheetId');
        $sheetIndexZeroBased = $sheetIdOneBased - 1;

        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $escaper = new \Box\Spout\Common\Escaper\XLSX();
        $sheetName = $escaper->unescape($escapedSheetName);

        return new Sheet($this->filePath, $sheetDataXMLFilePath, $this->sharedStringsHelper, $sheetIndexZeroBased, $sheetName);
    }

    /**
     * Returns a representation of the workbook.xml.rels file, ready to be parsed.
     * The returned value is cached.
     *
     * @return \Box\Spout\Reader\Wrapper\SimpleXMLElement XML element representating the workbook.xml.rels file
     */
    protected function getWorkbookXMLRelsAsXMLElement()
    {
        if (!$this->workbookXMLRelsAsXMLElement) {
            $this->workbookXMLRelsAsXMLElement = $this->getFileAsXMLElementWithNamespace(
                self::WORKBOOK_XML_RELS_FILE_PATH,
                self::MAIN_NAMESPACE_FOR_WORKBOOK_XML_RELS
            );
        }

        return $this->workbookXMLRelsAsXMLElement;
    }

    /**
     * Returns a representation of the workbook.xml file, ready to be parsed.
     * The returned value is cached.
     *
     * @return \Box\Spout\Reader\Wrapper\SimpleXMLElement XML element representating the workbook.xml.rels file
     */
    protected function getWorkbookXMLAsXMLElement()
    {
        if (!$this->workbookXMLAsXMLElement) {
            $this->workbookXMLAsXMLElement = $this->getFileAsXMLElementWithNamespace(
                self::WORKBOOK_XML_FILE_PATH,
                self::MAIN_NAMESPACE_FOR_WORKBOOK_XML
            );
        }

        return $this->workbookXMLAsXMLElement;
    }

    /**
     * Loads the contents of the given file in an XML parser and register the given XPath namespace.
     *
     * @param string $xmlFilePath The path of the XML file inside the XLSX file
     * @param string $mainNamespace The main XPath namespace to register
     * @return \Box\Spout\Reader\Wrapper\SimpleXMLElement The XML element representing the file
     */
    protected function getFileAsXMLElementWithNamespace($xmlFilePath, $mainNamespace)
    {
        $xmlContents = $this->globalFunctionsHelper->file_get_contents('zip://' . $this->filePath . '#' . $xmlFilePath);

        $xmlElement = new SimpleXMLElement($xmlContents);
        $xmlElement->registerXPathNamespace('ns', $mainNamespace);

        return $xmlElement;
    }
}
