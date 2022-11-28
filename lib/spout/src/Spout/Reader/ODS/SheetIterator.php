<?php

namespace Box\Spout\Reader\ODS;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\XMLProcessingException;
use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\ODS\Creator\InternalEntityFactory;
use Box\Spout\Reader\ODS\Helper\SettingsHelper;
use Box\Spout\Reader\Wrapper\XMLReader;

/**
 * Class SheetIterator
 * Iterate over ODS sheet.
 */
class SheetIterator implements IteratorInterface
{
    const CONTENT_XML_FILE_PATH = 'content.xml';

    const XML_STYLE_NAMESPACE = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';

    /** Definition of XML nodes name and attribute used to parse sheet data */
    const XML_NODE_AUTOMATIC_STYLES = 'office:automatic-styles';
    const XML_NODE_STYLE_TABLE_PROPERTIES = 'table-properties';
    const XML_NODE_TABLE = 'table:table';
    const XML_ATTRIBUTE_STYLE_NAME = 'style:name';
    const XML_ATTRIBUTE_TABLE_NAME = 'table:name';
    const XML_ATTRIBUTE_TABLE_STYLE_NAME = 'table:style-name';
    const XML_ATTRIBUTE_TABLE_DISPLAY = 'table:display';

    /** @var string Path of the file to be read */
    protected $filePath;

    /** @var \Box\Spout\Common\Manager\OptionsManagerInterface Reader's options manager */
    protected $optionsManager;

    /** @var InternalEntityFactory Factory to create entities */
    protected $entityFactory;

    /** @var XMLReader The XMLReader object that will help read sheet's XML data */
    protected $xmlReader;

    /** @var \Box\Spout\Common\Helper\Escaper\ODS Used to unescape XML data */
    protected $escaper;

    /** @var bool Whether there are still at least a sheet to be read */
    protected $hasFoundSheet;

    /** @var int The index of the sheet being read (zero-based) */
    protected $currentSheetIndex;

    /** @var string The name of the sheet that was defined as active */
    protected $activeSheetName;

    /** @var array Associative array [STYLE_NAME] => [IS_SHEET_VISIBLE] */
    protected $sheetsVisibility;

    /**
     * @param string $filePath Path of the file to be read
     * @param \Box\Spout\Common\Manager\OptionsManagerInterface $optionsManager
     * @param \Box\Spout\Common\Helper\Escaper\ODS $escaper Used to unescape XML data
     * @param SettingsHelper $settingsHelper Helper to get data from "settings.xml"
     * @param InternalEntityFactory $entityFactory Factory to create entities
     */
    public function __construct($filePath, $optionsManager, $escaper, $settingsHelper, $entityFactory)
    {
        $this->filePath = $filePath;
        $this->optionsManager = $optionsManager;
        $this->entityFactory = $entityFactory;
        $this->xmlReader = $entityFactory->createXMLReader();
        $this->escaper = $escaper;
        $this->activeSheetName = $settingsHelper->getActiveSheetName($filePath);
    }

    /**
     * Rewind the Iterator to the first element
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to open the XML file containing sheets' data
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->xmlReader->close();

        if ($this->xmlReader->openFileInZip($this->filePath, self::CONTENT_XML_FILE_PATH) === false) {
            $contentXmlFilePath = $this->filePath . '#' . self::CONTENT_XML_FILE_PATH;
            throw new IOException("Could not open \"{$contentXmlFilePath}\".");
        }

        try {
            $this->sheetsVisibility = $this->readSheetsVisibility();
            $this->hasFoundSheet = $this->xmlReader->readUntilNodeFound(self::XML_NODE_TABLE);
        } catch (XMLProcessingException $exception) {
            throw new IOException("The content.xml file is invalid and cannot be read. [{$exception->getMessage()}]");
        }

        $this->currentSheetIndex = 0;
    }

    /**
     * Extracts the visibility of the sheets
     *
     * @return array Associative array [STYLE_NAME] => [IS_SHEET_VISIBLE]
     */
    private function readSheetsVisibility()
    {
        $sheetsVisibility = [];

        $this->xmlReader->readUntilNodeFound(self::XML_NODE_AUTOMATIC_STYLES);
        $automaticStylesNode = $this->xmlReader->expand();

        $tableStyleNodes = $automaticStylesNode->getElementsByTagNameNS(self::XML_STYLE_NAMESPACE, self::XML_NODE_STYLE_TABLE_PROPERTIES);

        /** @var \DOMElement $tableStyleNode */
        foreach ($tableStyleNodes as $tableStyleNode) {
            $isSheetVisible = ($tableStyleNode->getAttribute(self::XML_ATTRIBUTE_TABLE_DISPLAY) !== 'false');

            $parentStyleNode = $tableStyleNode->parentNode;
            $styleName = $parentStyleNode->getAttribute(self::XML_ATTRIBUTE_STYLE_NAME);

            $sheetsVisibility[$styleName] = $isSheetVisible;
        }

        return $sheetsVisibility;
    }

    /**
     * Checks if current position is valid
     * @see http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->hasFoundSheet;
    }

    /**
     * Move forward to next element
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->hasFoundSheet = $this->xmlReader->readUntilNodeFound(self::XML_NODE_TABLE);

        if ($this->hasFoundSheet) {
            $this->currentSheetIndex++;
        }
    }

    /**
     * Return the current element
     * @see http://php.net/manual/en/iterator.current.php
     *
     * @return \Box\Spout\Reader\ODS\Sheet
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $escapedSheetName = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_TABLE_NAME);
        $sheetName = $this->escaper->unescape($escapedSheetName);

        $isSheetActive = $this->isSheetActive($sheetName, $this->currentSheetIndex, $this->activeSheetName);

        $sheetStyleName = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_TABLE_STYLE_NAME);
        $isSheetVisible = $this->isSheetVisible($sheetStyleName);

        return $this->entityFactory->createSheet(
            $this->xmlReader,
            $this->currentSheetIndex,
            $sheetName,
            $isSheetActive,
            $isSheetVisible,
            $this->optionsManager
        );
    }

    /**
     * Returns whether the current sheet was defined as the active one
     *
     * @param string $sheetName Name of the current sheet
     * @param int $sheetIndex Index of the current sheet
     * @param string|null $activeSheetName Name of the sheet that was defined as active or NULL if none defined
     * @return bool Whether the current sheet was defined as the active one
     */
    private function isSheetActive($sheetName, $sheetIndex, $activeSheetName)
    {
        // The given sheet is active if its name matches the defined active sheet's name
        // or if no information about the active sheet was found, it defaults to the first sheet.
        return (
            ($activeSheetName === null && $sheetIndex === 0) ||
            ($activeSheetName === $sheetName)
        );
    }

    /**
     * Returns whether the current sheet is visible
     *
     * @param string $sheetStyleName Name of the sheet style
     * @return bool Whether the current sheet is visible
     */
    private function isSheetVisible($sheetStyleName)
    {
        return isset($this->sheetsVisibility[$sheetStyleName]) ?
            $this->sheetsVisibility[$sheetStyleName] :
            true;
    }

    /**
     * Return the key of the current element
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->currentSheetIndex + 1;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function end()
    {
        $this->xmlReader->close();
    }
}
