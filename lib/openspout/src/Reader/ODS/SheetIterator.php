<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use DOMElement;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\ODS;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Exception\XMLProcessingException;
use OpenSpout\Reader\ODS\Helper\CellValueFormatter;
use OpenSpout\Reader\ODS\Helper\SettingsHelper;
use OpenSpout\Reader\SheetIteratorInterface;
use OpenSpout\Reader\Wrapper\XMLReader;

/**
 * @implements SheetIteratorInterface<Sheet>
 */
final class SheetIterator implements SheetIteratorInterface
{
    public const CONTENT_XML_FILE_PATH = 'content.xml';

    public const XML_STYLE_NAMESPACE = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';

    /**
     * Definition of XML nodes name and attribute used to parse sheet data.
     */
    public const XML_NODE_AUTOMATIC_STYLES = 'office:automatic-styles';
    public const XML_NODE_STYLE_TABLE_PROPERTIES = 'table-properties';
    public const XML_NODE_TABLE = 'table:table';
    public const XML_ATTRIBUTE_STYLE_NAME = 'style:name';
    public const XML_ATTRIBUTE_TABLE_NAME = 'table:name';
    public const XML_ATTRIBUTE_TABLE_STYLE_NAME = 'table:style-name';
    public const XML_ATTRIBUTE_TABLE_DISPLAY = 'table:display';

    /** @var string Path of the file to be read */
    private string $filePath;

    private Options $options;

    /** @var XMLReader The XMLReader object that will help read sheet's XML data */
    private XMLReader $xmlReader;

    /** @var ODS Used to unescape XML data */
    private ODS $escaper;

    /** @var bool Whether there are still at least a sheet to be read */
    private bool $hasFoundSheet;

    /** @var int The index of the sheet being read (zero-based) */
    private int $currentSheetIndex;

    /** @var string The name of the sheet that was defined as active */
    private ?string $activeSheetName;

    /** @var array<string, bool> Associative array [STYLE_NAME] => [IS_SHEET_VISIBLE] */
    private array $sheetsVisibility;

    public function __construct(
        string $filePath,
        Options $options,
        ODS $escaper,
        SettingsHelper $settingsHelper
    ) {
        $this->filePath = $filePath;
        $this->options = $options;
        $this->xmlReader = new XMLReader();
        $this->escaper = $escaper;
        $this->activeSheetName = $settingsHelper->getActiveSheetName($filePath);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to open the XML file containing sheets' data
     */
    public function rewind(): void
    {
        $this->xmlReader->close();

        if (false === $this->xmlReader->openFileInZip($this->filePath, self::CONTENT_XML_FILE_PATH)) {
            $contentXmlFilePath = $this->filePath.'#'.self::CONTENT_XML_FILE_PATH;

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
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        $valid = $this->hasFoundSheet;
        if (!$valid) {
            $this->xmlReader->close();
        }

        return $valid;
    }

    /**
     * Move forward to next element.
     *
     * @see http://php.net/manual/en/iterator.next.php
     */
    public function next(): void
    {
        $this->hasFoundSheet = $this->xmlReader->readUntilNodeFound(self::XML_NODE_TABLE);

        if ($this->hasFoundSheet) {
            ++$this->currentSheetIndex;
        }
    }

    /**
     * Return the current element.
     *
     * @see http://php.net/manual/en/iterator.current.php
     */
    public function current(): Sheet
    {
        $escapedSheetName = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_TABLE_NAME);
        \assert(null !== $escapedSheetName);
        $sheetName = $this->escaper->unescape($escapedSheetName);

        $isSheetActive = $this->isSheetActive($sheetName, $this->currentSheetIndex, $this->activeSheetName);

        $sheetStyleName = $this->xmlReader->getAttribute(self::XML_ATTRIBUTE_TABLE_STYLE_NAME);
        \assert(null !== $sheetStyleName);
        $isSheetVisible = $this->isSheetVisible($sheetStyleName);

        return new Sheet(
            new RowIterator(
                $this->options,
                new CellValueFormatter($this->options->SHOULD_FORMAT_DATES, new ODS()),
                new XMLProcessor($this->xmlReader)
            ),
            $this->currentSheetIndex,
            $sheetName,
            $isSheetActive,
            $isSheetVisible
        );
    }

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     */
    public function key(): int
    {
        return $this->currentSheetIndex + 1;
    }

    /**
     * Extracts the visibility of the sheets.
     *
     * @return array<string, bool> Associative array [STYLE_NAME] => [IS_SHEET_VISIBLE]
     */
    private function readSheetsVisibility(): array
    {
        $sheetsVisibility = [];

        $this->xmlReader->readUntilNodeFound(self::XML_NODE_AUTOMATIC_STYLES);

        $automaticStylesNode = $this->xmlReader->expand();
        \assert($automaticStylesNode instanceof DOMElement);

        $tableStyleNodes = $automaticStylesNode->getElementsByTagNameNS(self::XML_STYLE_NAMESPACE, self::XML_NODE_STYLE_TABLE_PROPERTIES);

        foreach ($tableStyleNodes as $tableStyleNode) {
            $isSheetVisible = ('false' !== $tableStyleNode->getAttribute(self::XML_ATTRIBUTE_TABLE_DISPLAY));

            $parentStyleNode = $tableStyleNode->parentNode;
            \assert($parentStyleNode instanceof DOMElement);
            $styleName = $parentStyleNode->getAttribute(self::XML_ATTRIBUTE_STYLE_NAME);

            $sheetsVisibility[$styleName] = $isSheetVisible;
        }

        return $sheetsVisibility;
    }

    /**
     * Returns whether the current sheet was defined as the active one.
     *
     * @param string      $sheetName       Name of the current sheet
     * @param int         $sheetIndex      Index of the current sheet
     * @param null|string $activeSheetName Name of the sheet that was defined as active or NULL if none defined
     *
     * @return bool Whether the current sheet was defined as the active one
     */
    private function isSheetActive(string $sheetName, int $sheetIndex, ?string $activeSheetName): bool
    {
        // The given sheet is active if its name matches the defined active sheet's name
        // or if no information about the active sheet was found, it defaults to the first sheet.
        return
            (null === $activeSheetName && 0 === $sheetIndex)
            || ($activeSheetName === $sheetName)
        ;
    }

    /**
     * Returns whether the current sheet is visible.
     *
     * @param string $sheetStyleName Name of the sheet style
     *
     * @return bool Whether the current sheet is visible
     */
    private function isSheetVisible(string $sheetStyleName): bool
    {
        return $this->sheetsVisibility[$sheetStyleName] ??
            true;
    }
}
