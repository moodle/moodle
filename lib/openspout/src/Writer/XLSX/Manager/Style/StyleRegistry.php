<?php

namespace OpenSpout\Writer\XLSX\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;

/**
 * Registry for all used styles.
 */
class StyleRegistry extends \OpenSpout\Writer\Common\Manager\Style\StyleRegistry
{
    /**
     * @see https://msdn.microsoft.com/en-us/library/ff529597(v=office.12).aspx
     *
     * @var array Mapping between built-in format and the associated numFmtId
     */
    protected static $builtinNumFormatToIdMapping = [
        'General' => 0,
        '0' => 1,
        '0.00' => 2,
        '#,##0' => 3,
        '#,##0.00' => 4,
        '$#,##0,\-$#,##0' => 5,
        '$#,##0,[Red]\-$#,##0' => 6,
        '$#,##0.00,\-$#,##0.00' => 7,
        '$#,##0.00,[Red]\-$#,##0.00' => 8,
        '0%' => 9,
        '0.00%' => 10,
        '0.00E+00' => 11,
        '# ?/?' => 12,
        '# ??/??' => 13,
        'mm-dd-yy' => 14,
        'd-mmm-yy' => 15,
        'd-mmm' => 16,
        'mmm-yy' => 17,
        'h:mm AM/PM' => 18,
        'h:mm:ss AM/PM' => 19,
        'h:mm' => 20,
        'h:mm:ss' => 21,
        'm/d/yy h:mm' => 22,

        '#,##0 ,(#,##0)' => 37,
        '#,##0 ,[Red](#,##0)' => 38,
        '#,##0.00,(#,##0.00)' => 39,
        '#,##0.00,[Red](#,##0.00)' => 40,

        '_("$"* #,##0.00_),_("$"* \(#,##0.00\),_("$"* "-"??_),_(@_)' => 44,
        'mm:ss' => 45,
        '[h]:mm:ss' => 46,
        'mm:ss.0' => 47,

        '##0.0E+0' => 48,
        '@' => 49,

        '[$-404]e/m/d' => 27,
        'm/d/yy' => 30,
        't0' => 59,
        't0.00' => 60,
        't#,##0' => 61,
        't#,##0.00' => 62,
        't0%' => 67,
        't0.00%' => 68,
        't# ?/?' => 69,
        't# ??/??' => 70,
    ];

    /**
     * @var array
     */
    protected $registeredFormats = [];

    /**
     * @var array [STYLE_ID] => [FORMAT_ID] maps a style to a format declaration
     */
    protected $styleIdToFormatsMappingTable = [];

    /**
     * If the numFmtId is lower than 0xA4 (164 in decimal)
     * then it's a built-in number format.
     * Since Excel is the dominant vendor - we play along here.
     *
     * @var int the fill index counter for custom fills
     */
    protected $formatIndex = 164;

    /**
     * @var array
     */
    protected $registeredFills = [];

    /**
     * @var array [STYLE_ID] => [FILL_ID] maps a style to a fill declaration
     */
    protected $styleIdToFillMappingTable = [];

    /**
     * Excel preserves two default fills with index 0 and 1
     * Since Excel is the dominant vendor - we play along here.
     *
     * @var int the fill index counter for custom fills
     */
    protected $fillIndex = 2;

    /**
     * @var array
     */
    protected $registeredBorders = [];

    /**
     * @var array [STYLE_ID] => [BORDER_ID] maps a style to a border declaration
     */
    protected $styleIdToBorderMappingTable = [];

    /**
     * XLSX specific operations on the registered styles.
     *
     * @return Style
     */
    public function registerStyle(Style $style)
    {
        if ($style->isRegistered()) {
            return $style;
        }

        $registeredStyle = parent::registerStyle($style);
        $this->registerFill($registeredStyle);
        $this->registerFormat($registeredStyle);
        $this->registerBorder($registeredStyle);

        return $registeredStyle;
    }

    /**
     * @param int $styleId
     *
     * @return null|int Format ID associated to the given style ID
     */
    public function getFormatIdForStyleId($styleId)
    {
        return $this->styleIdToFormatsMappingTable[$styleId] ?? null;
    }

    /**
     * @param int $styleId
     *
     * @return null|int Fill ID associated to the given style ID
     */
    public function getFillIdForStyleId($styleId)
    {
        return (isset($this->styleIdToFillMappingTable[$styleId])) ?
            $this->styleIdToFillMappingTable[$styleId] :
            null;
    }

    /**
     * @param int $styleId
     *
     * @return null|int Fill ID associated to the given style ID
     */
    public function getBorderIdForStyleId($styleId)
    {
        return (isset($this->styleIdToBorderMappingTable[$styleId])) ?
            $this->styleIdToBorderMappingTable[$styleId] :
            null;
    }

    /**
     * @return array
     */
    public function getRegisteredFills()
    {
        return $this->registeredFills;
    }

    /**
     * @return array
     */
    public function getRegisteredBorders()
    {
        return $this->registeredBorders;
    }

    /**
     * @return array
     */
    public function getRegisteredFormats()
    {
        return $this->registeredFormats;
    }

    /**
     * Register a format definition.
     */
    protected function registerFormat(Style $style)
    {
        $styleId = $style->getId();

        $format = $style->getFormat();
        if ($format) {
            $isFormatRegistered = isset($this->registeredFormats[$format]);

            // We need to track the already registered format definitions
            if ($isFormatRegistered) {
                $registeredStyleId = $this->registeredFormats[$format];
                $registeredFormatId = $this->styleIdToFormatsMappingTable[$registeredStyleId];
                $this->styleIdToFormatsMappingTable[$styleId] = $registeredFormatId;
            } else {
                $this->registeredFormats[$format] = $styleId;

                $id = self::$builtinNumFormatToIdMapping[$format] ?? $this->formatIndex++;
                $this->styleIdToFormatsMappingTable[$styleId] = $id;
            }
        } else {
            // The formatId maps a style to a format declaration
            // When there is no format definition - we default to 0 ( General )
            $this->styleIdToFormatsMappingTable[$styleId] = 0;
        }
    }

    /**
     * Register a fill definition.
     */
    private function registerFill(Style $style)
    {
        $styleId = $style->getId();

        // Currently - only solid backgrounds are supported
        // so $backgroundColor is a scalar value (RGB Color)
        $backgroundColor = $style->getBackgroundColor();

        if ($backgroundColor) {
            $isBackgroundColorRegistered = isset($this->registeredFills[$backgroundColor]);

            // We need to track the already registered background definitions
            if ($isBackgroundColorRegistered) {
                $registeredStyleId = $this->registeredFills[$backgroundColor];
                $registeredFillId = $this->styleIdToFillMappingTable[$registeredStyleId];
                $this->styleIdToFillMappingTable[$styleId] = $registeredFillId;
            } else {
                $this->registeredFills[$backgroundColor] = $styleId;
                $this->styleIdToFillMappingTable[$styleId] = $this->fillIndex++;
            }
        } else {
            // The fillId maps a style to a fill declaration
            // When there is no background color definition - we default to 0
            $this->styleIdToFillMappingTable[$styleId] = 0;
        }
    }

    /**
     * Register a border definition.
     */
    private function registerBorder(Style $style)
    {
        $styleId = $style->getId();

        if ($style->shouldApplyBorder()) {
            $border = $style->getBorder();
            $serializedBorder = serialize($border);

            $isBorderAlreadyRegistered = isset($this->registeredBorders[$serializedBorder]);

            if ($isBorderAlreadyRegistered) {
                $registeredStyleId = $this->registeredBorders[$serializedBorder];
                $registeredBorderId = $this->styleIdToBorderMappingTable[$registeredStyleId];
                $this->styleIdToBorderMappingTable[$styleId] = $registeredBorderId;
            } else {
                $this->registeredBorders[$serializedBorder] = $styleId;
                $this->styleIdToBorderMappingTable[$styleId] = \count($this->registeredBorders);
            }
        } else {
            // If no border should be applied - the mapping is the default border: 0
            $this->styleIdToBorderMappingTable[$styleId] = 0;
        }
    }
}
