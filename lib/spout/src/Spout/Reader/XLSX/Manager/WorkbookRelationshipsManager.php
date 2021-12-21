<?php

namespace Box\Spout\Reader\XLSX\Manager;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Wrapper\XMLReader;
use Box\Spout\Reader\XLSX\Creator\InternalEntityFactory;

/**
 * Class WorkbookRelationshipsManager
 * This class manages the workbook relationships defined in the associated XML file
 */
class WorkbookRelationshipsManager
{
    const BASE_PATH = 'xl/';

    /** Path of workbook relationships XML file inside the XLSX file */
    const WORKBOOK_RELS_XML_FILE_PATH = 'xl/_rels/workbook.xml.rels';

    /** Relationships types - For Transitional and Strict OOXML */
    const RELATIONSHIP_TYPE_SHARED_STRINGS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
    const RELATIONSHIP_TYPE_STYLES = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles';
    const RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT = 'http://purl.oclc.org/ooxml/officeDocument/relationships/sharedStrings';
    const RELATIONSHIP_TYPE_STYLES_STRICT = 'http://purl.oclc.org/ooxml/officeDocument/relationships/styles';

    /** Nodes and attributes used to find relevant information in the workbook relationships XML file */
    const XML_NODE_RELATIONSHIP = 'Relationship';
    const XML_ATTRIBUTE_TYPE = 'Type';
    const XML_ATTRIBUTE_TARGET = 'Target';

    /** @var string Path of the XLSX file being read */
    private $filePath;

    /** @var InternalEntityFactory Factory to create entities */
    private $entityFactory;

    /** @var array Cache of the already read workbook relationships: [TYPE] => [FILE_NAME] */
    private $cachedWorkbookRelationships;

    /**
     * @param string $filePath Path of the XLSX file being read
     * @param InternalEntityFactory $entityFactory Factory to create entities
     */
    public function __construct($filePath, $entityFactory)
    {
        $this->filePath = $filePath;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return string The path of the shared string XML file
     */
    public function getSharedStringsXMLFilePath()
    {
        $workbookRelationships = $this->getWorkbookRelationships();
        $sharedStringsXMLFilePath = $workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS]
            ?? $workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT];

        // the file path can be relative (e.g. "styles.xml") or absolute (e.g. "/xl/styles.xml")
        $doesContainBasePath = (\strpos($sharedStringsXMLFilePath, self::BASE_PATH) !== false);
        if (!$doesContainBasePath) {
            // make sure we return an absolute file path
            $sharedStringsXMLFilePath = self::BASE_PATH . $sharedStringsXMLFilePath;
        }

        return $sharedStringsXMLFilePath;
    }

    /**
     * @return bool Whether the XLSX file contains a shared string XML file
     */
    public function hasSharedStringsXMLFile()
    {
        $workbookRelationships = $this->getWorkbookRelationships();

        return isset($workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS])
            || isset($workbookRelationships[self::RELATIONSHIP_TYPE_SHARED_STRINGS_STRICT]);
    }

    /**
     * @return bool Whether the XLSX file contains a styles XML file
     */
    public function hasStylesXMLFile()
    {
        $workbookRelationships = $this->getWorkbookRelationships();

        return isset($workbookRelationships[self::RELATIONSHIP_TYPE_STYLES])
            || isset($workbookRelationships[self::RELATIONSHIP_TYPE_STYLES_STRICT]);
    }

    /**
     * @return string The path of the styles XML file
     */
    public function getStylesXMLFilePath()
    {
        $workbookRelationships = $this->getWorkbookRelationships();
        $stylesXMLFilePath = $workbookRelationships[self::RELATIONSHIP_TYPE_STYLES]
            ?? $workbookRelationships[self::RELATIONSHIP_TYPE_STYLES_STRICT];

        // the file path can be relative (e.g. "styles.xml") or absolute (e.g. "/xl/styles.xml")
        $doesContainBasePath = (\strpos($stylesXMLFilePath, self::BASE_PATH) !== false);
        if (!$doesContainBasePath) {
            // make sure we return a full path
            $stylesXMLFilePath = self::BASE_PATH . $stylesXMLFilePath;
        }

        return $stylesXMLFilePath;
    }

    /**
     * Reads the workbook.xml.rels and extracts the filename associated to the different types.
     * It caches the result so that the file is read only once.
     *
     * @throws \Box\Spout\Common\Exception\IOException If workbook.xml.rels can't be read
     * @return array
     */
    private function getWorkbookRelationships()
    {
        if (!isset($this->cachedWorkbookRelationships)) {
            $xmlReader = $this->entityFactory->createXMLReader();

            if ($xmlReader->openFileInZip($this->filePath, self::WORKBOOK_RELS_XML_FILE_PATH) === false) {
                throw new IOException('Could not open "' . self::WORKBOOK_RELS_XML_FILE_PATH . '".');
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
     *
     * @param XMLReader $xmlReader
     * @return void
     */
    private function processWorkbookRelationship($xmlReader)
    {
        $type = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TYPE);
        $target = $xmlReader->getAttribute(self::XML_ATTRIBUTE_TARGET);

        // @NOTE: if a type is defined more than once, we overwrite the previous value
        // To be changed if we want to get the file paths of sheet XML files for instance.
        $this->cachedWorkbookRelationships[$type] = $target;
    }
}
