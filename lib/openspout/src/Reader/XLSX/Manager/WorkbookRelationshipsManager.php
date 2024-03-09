<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Wrapper\XMLReader;

/**
 * @internal
 */
final class WorkbookRelationshipsManager
{
    public const BASE_PATH = 'xl/';

    /**
     * Path of workbook relationships XML file inside the XLSX file.
     */
    public const WORKBOOK_RELS_XML_FILE_PATH = 'xl/_rels/workbook.xml.rels';

    /**
     * Relationships types - For Transitional and Strict OOXML.
     */
    public const RELATIONSHIP_TYPE_SHARED_STRINGS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
    public const RELATIONSHIP_TYPE_STYLES = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles';
    public const RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT = 'http://purl.oclc.org/ooxml/officeDocument/relationships/sharedStrings';
    public const RELATIONSHIP_TYPE_STYLES_STRICT = 'http://purl.oclc.org/ooxml/officeDocument/relationships/styles';

    /**
     * Nodes and attributes used to find relevant information in the workbook relationships XML file.
     */
    public const XML_NODE_RELATIONSHIP = 'Relationship';
    public const XML_ATTRIBUTE_TYPE = 'Type';
    public const XML_ATTRIBUTE_TARGET = 'Target';

    /** @var string Path of the XLSX file being read */
    private readonly string $filePath;

    /** @var array<string, string> Cache of the already read workbook relationships: [TYPE] => [FILE_NAME] */
    private array $cachedWorkbookRelationships;

    /**
     * @param string $filePath Path of the XLSX file being read
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string The path of the shared string XML file
     */
    public function getSharedStringsXMLFilePath(): string
    {
        $workbookRelationships = $this->getWorkbookRelationships();
        $sharedStringsXMLFilePath = $workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS]
            ?? $workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT];

        // the file path can be relative (e.g. "styles.xml") or absolute (e.g. "/xl/styles.xml")
        $doesContainBasePath = str_contains($sharedStringsXMLFilePath, self::BASE_PATH);
        if (!$doesContainBasePath) {
            // make sure we return an absolute file path
            $sharedStringsXMLFilePath = self::BASE_PATH.$sharedStringsXMLFilePath;
        }

        return $sharedStringsXMLFilePath;
    }

    /**
     * @return bool Whether the XLSX file contains a shared string XML file
     */
    public function hasSharedStringsXMLFile(): bool
    {
        $workbookRelationships = $this->getWorkbookRelationships();

        return isset($workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS])
            || isset($workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT]);
    }

    /**
     * @return bool Whether the XLSX file contains a styles XML file
     */
    public function hasStylesXMLFile(): bool
    {
        $workbookRelationships = $this->getWorkbookRelationships();

        return isset($workbookRelationships[self::RELATIONSHIP_TYPE_STYLES])
            || isset($workbookRelationships[self::RELATIONSHIP_TYPE_STYLES_STRICT]);
    }

    /**
     * @return string The path of the styles XML file
     */
    public function getStylesXMLFilePath(): string
    {
        $workbookRelationships = $this->getWorkbookRelationships();
        $stylesXMLFilePath = $workbookRelationships[self::RELATIONSHIP_TYPE_STYLES]
            ?? $workbookRelationships[self::RELATIONSHIP_TYPE_STYLES_STRICT];

        // the file path can be relative (e.g. "styles.xml") or absolute (e.g. "/xl/styles.xml")
        $doesContainBasePath = str_contains($stylesXMLFilePath, self::BASE_PATH);
        if (!$doesContainBasePath) {
            // make sure we return a full path
            $stylesXMLFilePath = self::BASE_PATH.$stylesXMLFilePath;
        }

        return $stylesXMLFilePath;
    }

    /**
     * Reads the workbook.xml.rels and extracts the filename associated to the different types.
     * It caches the result so that the file is read only once.
     *
     * @return array<string, string>
     *
     * @throws IOException If workbook.xml.rels can't be read
     */
    private function getWorkbookRelationships(): array
    {
        if (!isset($this->cachedWorkbookRelationships)) {
            $xmlReader = new XMLReader();

            if (false === $xmlReader->openFileInZip($this->filePath, self::WORKBOOK_RELS_XML_FILE_PATH)) {
                throw new IOException('Could not open "'.self::WORKBOOK_RELS_XML_FILE_PATH.'".');
            }

            $this->cachedWorkbookRelationships = [];

            while ($xmlReader->readUntilNodeFound(self::XML_NODE_RELATIONSHIP)) {
                $this->processWorkbookRelationship($xmlReader);
            }
        }

        return $this->cachedWorkbookRelationships;
    }

    /**
     * Extracts and store the data of the current workbook relationship.
     */
    private function processWorkbookRelationship(XMLReader $xmlReader): void
    {
        $type = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TYPE);
        $target = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TARGET);
        \assert(null !== $target);

        // @NOTE: if a type is defined more than once, we overwrite the previous value
        // To be changed if we want to get the file paths of sheet XML files for instance.
        $this->cachedWorkbookRelationships[$type] = $target;
    }
}
