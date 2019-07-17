<?php

namespace Box\Spout\Reader\ODS\Helper;

use Box\Spout\Reader\Exception\XMLProcessingException;
use Box\Spout\Reader\ODS\Creator\InternalEntityFactory;

/**
 * Class SettingsHelper
 * This class provides helper functions to extract data from the "settings.xml" file.
 */
class SettingsHelper
{
    const SETTINGS_XML_FILE_PATH = 'settings.xml';

    /** Definition of XML nodes name and attribute used to parse settings data */
    const XML_NODE_CONFIG_ITEM = 'config:config-item';
    const XML_ATTRIBUTE_CONFIG_NAME = 'config:name';
    const XML_ATTRIBUTE_VALUE_ACTIVE_TABLE = 'ActiveTable';

    /** @var InternalEntityFactory Factory to create entities */
    private $entityFactory;

    /**
     * @param InternalEntityFactory $entityFactory Factory to create entities
     */
    public function __construct($entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * @param string $filePath Path of the file to be read
     * @return string|null Name of the sheet that was defined as active or NULL if none found
     */
    public function getActiveSheetName($filePath)
    {
        $xmlReader = $this->entityFactory->createXMLReader();
        if ($xmlReader->openFileInZip($filePath, self::SETTINGS_XML_FILE_PATH) === false) {
            return null;
        }

        $activeSheetName = null;

        try {
            while ($xmlReader->readUntilNodeFound(self::XML_NODE_CONFIG_ITEM)) {
                if ($xmlReader->getAttribute(self::XML_ATTRIBUTE_CONFIG_NAME) === self::XML_ATTRIBUTE_VALUE_ACTIVE_TABLE) {
                    $activeSheetName = $xmlReader->readString();
                    break;
                }
            }
        } catch (XMLProcessingException $exception) {
            // do nothing
        }

        $xmlReader->close();

        return $activeSheetName;
    }
}
