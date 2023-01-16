<?php

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Reader\XLSX\Creator\InternalEntityFactory;

/**
 * This class manages XLSX styles.
 */
class StyleManager
{
    /** Nodes used to find relevant information in the styles XML file */
    public const XML_NODE_NUM_FMTS = 'numFmts';
    public const XML_NODE_NUM_FMT = 'numFmt';
    public const XML_NODE_CELL_XFS = 'cellXfs';
    public const XML_NODE_XF = 'xf';

    /** Attributes used to find relevant information in the styles XML file */
    public const XML_ATTRIBUTE_NUM_FMT_ID = 'numFmtId';
    public const XML_ATTRIBUTE_FORMAT_CODE = 'formatCode';
    public const XML_ATTRIBUTE_APPLY_NUMBER_FORMAT = 'applyNumberFormat';

    /** By convention, default style ID is 0 */
    public const DEFAULT_STYLE_ID = 0;

    public const NUMBER_FORMAT_GENERAL = 'General';

    /**
     * @see https://msdn.microsoft.com/en-us/library/ff529597(v=office.12).aspx
     *
     * @var array Mapping between built-in numFmtId and the associated format - for dates only
     */
    protected static $builtinNumFmtIdToNumFormatMapping = [
        14 => 'm/d/yyyy', // @NOTE: ECMA spec is 'mm-dd-yy'
        15 => 'd-mmm-yy',
        16 => 'd-mmm',
        17 => 'mmm-yy',
        18 => 'h:mm AM/PM',
        19 => 'h:mm:ss AM/PM',
        20 => 'h:mm',
        21 => 'h:mm:ss',
        22 => 'm/d/yyyy h:mm', // @NOTE: ECMA spec is 'm/d/yy h:mm',
        45 => 'mm:ss',
        46 => '[h]:mm:ss',
        47 => 'mm:ss.0',  // @NOTE: ECMA spec is 'mmss.0',
    ];

    /** @var string Path of the XLSX file being read */
    protected $filePath;

    /** @var bool Whether the XLSX file contains a styles XML file */
    protected $hasStylesXMLFile;

    /** @var null|string Path of the styles XML file */
    protected $stylesXMLFilePath;

    /** @var InternalEntityFactory Factory to create entities */
    protected $entityFactory;

    /** @var array Array containing the IDs of built-in number formats indicating a date */
    protected $builtinNumFmtIdIndicatingDates;

    /** @var null|array Array containing a mapping NUM_FMT_ID => FORMAT_CODE */
    protected $customNumberFormats;

    /** @var null|array Array containing a mapping STYLE_ID => [STYLE_ATTRIBUTES] */
    protected $stylesAttributes;

    /** @var array Cache containing a mapping NUM_FMT_ID => IS_DATE_FORMAT. Used to avoid lots of recalculations */
    protected $numFmtIdToIsDateFormatCache = [];

    /**
     * @param string                       $filePath                     Path of the XLSX file being read
     * @param WorkbookRelationshipsManager $workbookRelationshipsManager Helps retrieving workbook relationships
     * @param InternalEntityFactory        $entityFactory                Factory to create entities
     */
    public function __construct($filePath, $workbookRelationshipsManager, $entityFactory)
    {
        $this->filePath = $filePath;
        $this->entityFactory = $entityFactory;
        $this->builtinNumFmtIdIndicatingDates = array_keys(self::$builtinNumFmtIdToNumFormatMapping);
        $this->hasStylesXMLFile = $workbookRelationshipsManager->hasStylesXMLFile();
        if ($this->hasStylesXMLFile) {
            $this->stylesXMLFilePath = $workbookRelationshipsManager->getStylesXMLFilePath();
        }
    }

    /**
     * Returns whether the style with the given ID should consider
     * numeric values as timestamps and format the cell as a date.
     *
     * @param int $styleId Zero-based style ID
     *
     * @return bool Whether the cell with the given cell should display a date instead of a numeric value
     */
    public function shouldFormatNumericValueAsDate($styleId)
    {
        if (!$this->hasStylesXMLFile) {
            return false;
        }

        $stylesAttributes = $this->getStylesAttributes();

        // Default style (0) does not format numeric values as timestamps. Only custom styles do.
        // Also if the style ID does not exist in the styles.xml file, format as numeric value.
        // Using isset here because it is way faster than array_key_exists...
        if (self::DEFAULT_STYLE_ID === $styleId || !isset($stylesAttributes[$styleId])) {
            return false;
        }

        $styleAttributes = $stylesAttributes[$styleId];

        return $this->doesStyleIndicateDate($styleAttributes);
    }

    /**
     * Returns the format as defined in "styles.xml" of the given style.
     * NOTE: It is assumed that the style DOES have a number format associated to it.
     *
     * @param int $styleId Zero-based style ID
     *
     * @return string The number format code associated with the given style
     */
    public function getNumberFormatCode($styleId)
    {
        $stylesAttributes = $this->getStylesAttributes();
        $styleAttributes = $stylesAttributes[$styleId];
        $numFmtId = $styleAttributes[self::XML_ATTRIBUTE_NUM_FMT_ID];

        if ($this->isNumFmtIdBuiltInDateFormat($numFmtId)) {
            $numberFormatCode = self::$builtinNumFmtIdToNumFormatMapping[$numFmtId];
        } else {
            $customNumberFormats = $this->getCustomNumberFormats();
            $numberFormatCode = $customNumberFormats[$numFmtId];
        }

        return $numberFormatCode;
    }

    /**
     * Reads the styles.xml file and extract the relevant information from the file.
     */
    protected function extractRelevantInfo()
    {
        $this->customNumberFormats = [];
        $this->stylesAttributes = [];

        $xmlReader = $this->entityFactory->createXMLReader();

        if ($xmlReader->openFileInZip($this->filePath, $this->stylesXMLFilePath)) {
            while ($xmlReader->read()) {
                if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_NUM_FMTS)) {
                    $this->extractNumberFormats($xmlReader);
                } elseif ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_CELL_XFS)) {
                    $this->extractStyleAttributes($xmlReader);
                }
            }

            $xmlReader->close();
        }
    }

    /**
     * Extracts number formats from the "numFmt" nodes.
     * For simplicity, the styles attributes are kept in memory. This is possible thanks
     * to the reuse of formats. So 1 million cells should not use 1 million formats.
     *
     * @param \OpenSpout\Reader\Wrapper\XMLReader $xmlReader XML Reader positioned on the "numFmts" node
     */
    protected function extractNumberFormats($xmlReader)
    {
        while ($xmlReader->read()) {
            if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_NUM_FMT)) {
                $numFmtId = (int) ($xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_FMT_ID));
                $formatCode = $xmlReader->getAttribute(self::XML_ATTRIBUTE_FORMAT_CODE);
                $this->customNumberFormats[$numFmtId] = $formatCode;
            } elseif ($xmlReader->isPositionedOnEndingNode(self::XML_NODE_NUM_FMTS)) {
                // Once done reading "numFmts" node's children
                break;
            }
        }
    }

    /**
     * Extracts style attributes from the "xf" nodes, inside the "cellXfs" section.
     * For simplicity, the styles attributes are kept in memory. This is possible thanks
     * to the reuse of styles. So 1 million cells should not use 1 million styles.
     *
     * @param \OpenSpout\Reader\Wrapper\XMLReader $xmlReader XML Reader positioned on the "cellXfs" node
     */
    protected function extractStyleAttributes($xmlReader)
    {
        while ($xmlReader->read()) {
            if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_XF)) {
                $numFmtId = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_FMT_ID);
                $normalizedNumFmtId = (null !== $numFmtId) ? (int) $numFmtId : null;

                $applyNumberFormat = $xmlReader->getAttribute(self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT);
                $normalizedApplyNumberFormat = (null !== $applyNumberFormat) ? (bool) $applyNumberFormat : null;

                $this->stylesAttributes[] = [
                    self::XML_ATTRIBUTE_NUM_FMT_ID => $normalizedNumFmtId,
                    self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT => $normalizedApplyNumberFormat,
                ];
            } elseif ($xmlReader->isPositionedOnEndingNode(self::XML_NODE_CELL_XFS)) {
                // Once done reading "cellXfs" node's children
                break;
            }
        }
    }

    /**
     * @return array The custom number formats
     */
    protected function getCustomNumberFormats()
    {
        if (!isset($this->customNumberFormats)) {
            $this->extractRelevantInfo();
        }

        return $this->customNumberFormats;
    }

    /**
     * @return array The styles attributes
     */
    protected function getStylesAttributes()
    {
        if (!isset($this->stylesAttributes)) {
            $this->extractRelevantInfo();
        }

        return $this->stylesAttributes;
    }

    /**
     * @param array $styleAttributes Array containing the style attributes (2 keys: "applyNumberFormat" and "numFmtId")
     *
     * @return bool Whether the style with the given attributes indicates that the number is a date
     */
    protected function doesStyleIndicateDate($styleAttributes)
    {
        $applyNumberFormat = $styleAttributes[self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT];
        $numFmtId = $styleAttributes[self::XML_ATTRIBUTE_NUM_FMT_ID];

        // A style may apply a date format if it has:
        //  - "applyNumberFormat" attribute not set to "false"
        //  - "numFmtId" attribute set
        // This is a preliminary check, as having "numFmtId" set just means the style should apply a specific number format,
        // but this is not necessarily a date.
        if (false === $applyNumberFormat || null === $numFmtId) {
            return false;
        }

        return $this->doesNumFmtIdIndicateDate($numFmtId);
    }

    /**
     * Returns whether the number format ID indicates that the number is a date.
     * The result is cached to avoid recomputing the same thing over and over, as
     * "numFmtId" attributes can be shared between multiple styles.
     *
     * @param int $numFmtId
     *
     * @return bool Whether the number format ID indicates that the number is a date
     */
    protected function doesNumFmtIdIndicateDate($numFmtId)
    {
        if (!isset($this->numFmtIdToIsDateFormatCache[$numFmtId])) {
            $formatCode = $this->getFormatCodeForNumFmtId($numFmtId);

            $this->numFmtIdToIsDateFormatCache[$numFmtId] = (
                $this->isNumFmtIdBuiltInDateFormat($numFmtId)
                || $this->isFormatCodeCustomDateFormat($formatCode)
            );
        }

        return $this->numFmtIdToIsDateFormatCache[$numFmtId];
    }

    /**
     * @param int $numFmtId
     *
     * @return null|string The custom number format or NULL if none defined for the given numFmtId
     */
    protected function getFormatCodeForNumFmtId($numFmtId)
    {
        $customNumberFormats = $this->getCustomNumberFormats();

        // Using isset here because it is way faster than array_key_exists...
        return (isset($customNumberFormats[$numFmtId])) ? $customNumberFormats[$numFmtId] : null;
    }

    /**
     * @param int $numFmtId
     *
     * @return bool Whether the number format ID indicates that the number is a date
     */
    protected function isNumFmtIdBuiltInDateFormat($numFmtId)
    {
        return \in_array($numFmtId, $this->builtinNumFmtIdIndicatingDates, true);
    }

    /**
     * @param null|string $formatCode
     *
     * @return bool Whether the given format code indicates that the number is a date
     */
    protected function isFormatCodeCustomDateFormat($formatCode)
    {
        // if no associated format code or if using the default "General" format
        if (null === $formatCode || 0 === strcasecmp($formatCode, self::NUMBER_FORMAT_GENERAL)) {
            return false;
        }

        return $this->isFormatCodeMatchingDateFormatPattern($formatCode);
    }

    /**
     * @param string $formatCode
     *
     * @return bool Whether the given format code matches a date format pattern
     */
    protected function isFormatCodeMatchingDateFormatPattern($formatCode)
    {
        // Remove extra formatting (what's between [ ], the brackets should not be preceded by a "\")
        $pattern = '((?<!\\\)\[.+?(?<!\\\)\])';
        $formatCode = preg_replace($pattern, '', $formatCode);

        // custom date formats contain specific characters to represent the date:
        // e - yy - m - d - h - s
        // and all of their variants (yyyy - mm - dd...)
        $dateFormatCharacters = ['e', 'yy', 'm', 'd', 'h', 's'];

        $hasFoundDateFormatCharacter = false;
        foreach ($dateFormatCharacters as $dateFormatCharacter) {
            // character not preceded by "\" (case insensitive)
            $pattern = '/(?<!\\\)'.$dateFormatCharacter.'/i';

            if (preg_match($pattern, $formatCode)) {
                $hasFoundDateFormatCharacter = true;

                break;
            }
        }

        return $hasFoundDateFormatCharacter;
    }
}
