<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS\Helper;

use OpenSpout\Reader\Exception\XMLProcessingException;
use OpenSpout\Reader\Wrapper\XMLReader;

/**
 * @internal
 */
final class SettingsHelper
{
    public const SETTINGS_XML_FILE_PATH = 'settings.xml';

    /**
     * Definition of XML nodes name and attribute used to parse settings data.
     */
    public const XML_NODE_CONFIG_ITEM = 'config:config-item';
    public const XML_ATTRIBUTE_CONFIG_NAME = 'config:name';
    public const XML_ATTRIBUTE_VALUE_ACTIVE_TABLE = 'ActiveTable';

    /**
     * @param string $filePath Path of the file to be read
     *
     * @return null|string Name of the sheet that was defined as active or NULL if none found
     */
    public function getActiveSheetName(string $filePath): ?string
    {
        $xmlReader = new XMLReader();
        if (false === $xmlReader->openFileInZip($filePath, self::SETTINGS_XML_FILE_PATH)) {
            return null;
        }

        $activeSheetName = null;

        try {
            while ($xmlReader->readUntilNodeFound(self::XML_NODE_CONFIG_ITEM)) {
                if (self::XML_ATTRIBUTE_VALUE_ACTIVE_TABLE === $xmlReader->getAttribute(self::XML_ATTRIBUTE_CONFIG_NAME)) {
                    $activeSheetName = $xmlReader->readString();

                    break;
                }
            }
        } catch (XMLProcessingException $exception) {  // @codeCoverageIgnore
            // do nothing
        }

        $xmlReader->close();

        return $activeSheetName;
    }
}
