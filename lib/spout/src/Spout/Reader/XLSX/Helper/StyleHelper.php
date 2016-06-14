<?php

namespace Box\Spout\Reader\XLSX\Helper;

use Box\Spout\Reader\Wrapper\SimpleXMLElement;
use Box\Spout\Reader\Wrapper\XMLReader;

/**
 * Class StyleHelper
 * This class provides helper functions related to XLSX styles
 *
 * @package Box\Spout\Reader\XLSX\Helper
 */
class StyleHelper
{
    /** Paths of XML files relative to the XLSX file root */
    const STYLES_XML_FILE_PATH = 'xl/styles.xml';

    /** Nodes used to find relevant information in the styles XML file */
    const XML_NODE_NUM_FMTS = 'numFmts';
    const XML_NODE_NUM_FMT = 'numFmt';
    const XML_NODE_CELL_XFS = 'cellXfs';
    const XML_NODE_XF = 'xf';

    /** Attributes used to find relevant information in the styles XML file */
    const XML_ATTRIBUTE_NUM_FMT_ID = 'numFmtId';
    const XML_ATTRIBUTE_FORMAT_CODE = 'formatCode';
    const XML_ATTRIBUTE_APPLY_NUMBER_FORMAT = 'applyNumberFormat';

    /** By convention, default style ID is 0 */
    const DEFAULT_STYLE_ID = 0;

    /** @var string Path of the XLSX file being read */
    protected $filePath;

    /** @var array Array containing a mapping NUM_FMT_ID => FORMAT_CODE */
    protected $customNumberFormats;

    /** @var array Array containing a mapping STYLE_ID => [STYLE_ATTRIBUTES] */
    protected $stylesAttributes;

    /**
     * @param string $filePath Path of the XLSX file being read
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Reads the styles.xml file and extract the relevant information from the file.
     *
     * @return void
     */
    protected function extractRelevantInfo()
    {
        $this->customNumberFormats = [];
        $this->stylesAttributes = [];

        $stylesXmlFilePath = $this->filePath .'#' . self::STYLES_XML_FILE_PATH;
        $xmlReader = new XMLReader();

        if ($xmlReader->open('zip://' . $stylesXmlFilePath)) {
            while ($xmlReader->read()) {
                if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_NUM_FMTS)) {
                    $numFmtsNode = new SimpleXMLElement($xmlReader->readOuterXml());
                    $this->extractNumberFormats($numFmtsNode);

                } else if ($xmlReader->isPositionedOnStartingNode(self::XML_NODE_CELL_XFS)) {
                    $cellXfsNode = new SimpleXMLElement($xmlReader->readOuterXml());
                    $this->extractStyleAttributes($cellXfsNode);
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
     * @param SimpleXMLElement $numFmtsNode The "numFmts" node
     * @return void
     */
    protected function extractNumberFormats($numFmtsNode)
    {
        foreach ($numFmtsNode->children() as $numFmtNode) {
            $numFmtId = intval($numFmtNode->getAttribute(self::XML_ATTRIBUTE_NUM_FMT_ID));
            $formatCode = $numFmtNode->getAttribute(self::XML_ATTRIBUTE_FORMAT_CODE);
            $this->customNumberFormats[$numFmtId] = $formatCode;
        }
    }

    /**
     * Extracts style attributes from the "xf" nodes, inside the "cellXfs" section.
     * For simplicity, the styles attributes are kept in memory. This is possible thanks
     * to the reuse of styles. So 1 million cells should not use 1 million styles.
     *
     * @param SimpleXMLElement $cellXfsNode The "cellXfs" node
     * @return void
     */
    protected function extractStyleAttributes($cellXfsNode)
    {
        foreach ($cellXfsNode->children() as $xfNode) {
            $this->stylesAttributes[] = [
                self::XML_ATTRIBUTE_NUM_FMT_ID => intval($xfNode->getAttribute(self::XML_ATTRIBUTE_NUM_FMT_ID)),
                self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT => !!($xfNode->getAttribute(self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT)),
            ];
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
     * Returns whether the style with the given ID should consider
     * numeric values as timestamps and format the cell as a date.
     *
     * @param int $styleId Zero-based style ID
     * @return bool Whether the cell with the given cell should display a date instead of a numeric value
     */
    public function shouldFormatNumericValueAsDate($styleId)
    {
        $stylesAttributes = $this->getStylesAttributes();

        // Default style (0) does not format numeric values as timestamps. Only custom styles do.
        // Also if the style ID does not exist in the styles.xml file, format as numeric value.
        // Using isset here because it is way faster than array_key_exists...
        if ($styleId === self::DEFAULT_STYLE_ID || !isset($stylesAttributes[$styleId])) {
            return false;
        }

        $styleAttributes = $stylesAttributes[$styleId];

        $applyNumberFormat = $styleAttributes[self::XML_ATTRIBUTE_APPLY_NUMBER_FORMAT];
        if (!$applyNumberFormat) {
            return false;
        }

        $numFmtId = $styleAttributes[self::XML_ATTRIBUTE_NUM_FMT_ID];
        return $this->doesNumFmtIdIndicateDate($numFmtId);
    }

    /**
     * @param int $numFmtId
     * @return bool Whether the number format ID indicates that the number is a timestamp
     */
    protected function doesNumFmtIdIndicateDate($numFmtId)
    {
        return (
            $this->isNumFmtIdBuiltInDateFormat($numFmtId) ||
            $this->isNumFmtIdCustomDateFormat($numFmtId)
        );
    }

    /**
     * @param int $numFmtId
     * @return bool Whether the number format ID indicates that the number is a timestamp
     */
    protected function isNumFmtIdBuiltInDateFormat($numFmtId)
    {
        $builtInDateFormatIds = [14, 15, 16, 17, 18, 19, 20, 21, 22, 45, 46, 47];
        return in_array($numFmtId, $builtInDateFormatIds);
    }

    /**
     * @param int $numFmtId
     * @return bool Whether the number format ID indicates that the number is a timestamp
     */
    protected function isNumFmtIdCustomDateFormat($numFmtId)
    {
        $customNumberFormats = $this->getCustomNumberFormats();

        // Using isset here because it is way faster than array_key_exists...
        if (!isset($customNumberFormats[$numFmtId])) {
            return false;
        }

        $customNumberFormat = $customNumberFormats[$numFmtId];

        // Remove extra formatting (what's between [ ], the brackets should not be preceded by a "\")
        $pattern = '((?<!\\\)\[.+?(?<!\\\)\])';
        $customNumberFormat = preg_replace($pattern, '', $customNumberFormat);

        // custom date formats contain specific characters to represent the date:
        // e - yy - m - d - h - s
        // and all of their variants (yyyy - mm - dd...)
        $dateFormatCharacters = ['e', 'yy', 'm', 'd', 'h', 's'];

        $hasFoundDateFormatCharacter = false;
        foreach ($dateFormatCharacters as $dateFormatCharacter) {
            // character not preceded by "\"
            $pattern = '/(?<!\\\)' . $dateFormatCharacter . '/';

            if (preg_match($pattern, $customNumberFormat)) {
                $hasFoundDateFormatCharacter = true;
                break;
            }
        }

        return $hasFoundDateFormatCharacter;
    }
}
