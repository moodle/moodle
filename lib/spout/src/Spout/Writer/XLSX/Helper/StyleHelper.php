<?php

namespace Box\Spout\Writer\XLSX\Helper;

use Box\Spout\Writer\Common\Helper\AbstractStyleHelper;
use Box\Spout\Writer\Style\Color;

/**
 * Class StyleHelper
 * This class provides helper functions to manage styles
 *
 * @package Box\Spout\Writer\XLSX\Helper
 */
class StyleHelper extends AbstractStyleHelper
{
    /**
     * Returns the content of the "styles.xml" file, given a list of styles.
     *
     * @return string
     */
    public function getStylesXMLFileContent()
    {
        $content = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
EOD;

        $content .= $this->getFontsSectionContent();
        $content .= $this->getFillsSectionContent();
        $content .= $this->getBordersSectionContent();
        $content .= $this->getCellStyleXfsSectionContent();
        $content .= $this->getCellXfsSectionContent();
        $content .= $this->getCellStylesSectionContent();

        $content .= <<<EOD
</styleSheet>
EOD;

        return $content;
    }

    /**
     * Returns the content of the "<fonts>" section.
     *
     * @return string
     */
    protected function getFontsSectionContent()
    {
        $content = '<fonts count="' . count($this->styleIdToStyleMappingTable) . '">';

        /** @var \Box\Spout\Writer\Style\Style $style */
        foreach ($this->getRegisteredStyles() as $style) {
            $content .= '<font>';

            $content .= '<sz val="' . $style->getFontSize() . '"/>';
            $content .= '<color rgb="' . Color::toARGB($style->getFontColor()) . '"/>';
            $content .= '<name val="' . $style->getFontName() . '"/>';

            if ($style->isFontBold()) {
                $content .= '<b/>';
            }
            if ($style->isFontItalic()) {
                $content .= '<i/>';
            }
            if ($style->isFontUnderline()) {
                $content .= '<u/>';
            }
            if ($style->isFontStrikethrough()) {
                $content .= '<strike/>';
            }

            $content .= '</font>';
        }

        $content .= '</fonts>';

        return $content;
    }

    /**
     * Returns the content of the "<fills>" section.
     *
     * @return string
     */
    protected function getFillsSectionContent()
    {
        return <<<EOD
<fills count="1">
    <fill>
        <patternFill patternType="none"/>
    </fill>
</fills>
EOD;
    }

    /**
     * Returns the content of the "<borders>" section.
     *
     * @return string
     */
    protected function getBordersSectionContent()
    {
        return <<<EOD
<borders count="1">
    <border>
        <left/>
        <right/>
        <top/>
        <bottom/>
        <diagonal/>
    </border>
</borders>
EOD;
    }

    /**
     * Returns the content of the "<cellStyleXfs>" section.
     *
     * @return string
     */
    protected function getCellStyleXfsSectionContent()
    {
        return <<<EOD
<cellStyleXfs count="1">
    <xf borderId="0" fillId="0" fontId="0" numFmtId="0"/>
</cellStyleXfs>
EOD;
    }

    /**
     * Returns the content of the "<cellXfs>" section.
     *
     * @return string
     */
    protected function getCellXfsSectionContent()
    {
        $registeredStyles = $this->getRegisteredStyles();

        $content = '<cellXfs count="' . count($registeredStyles) . '">';

        foreach ($registeredStyles as $style) {
            $content .= '<xf numFmtId="0" fontId="' . $style->getId() . '" fillId="0" borderId="0" xfId="0"';

            if ($style->shouldApplyFont()) {
                $content .= ' applyFont="1"';
            }

            if ($style->shouldWrapText()) {
                $content .= ' applyAlignment="1">';
                $content .= '<alignment wrapText="1"/>';
                $content .= '</xf>';
            } else {
                $content .= '/>';
            }
        }

        $content .= '</cellXfs>';

        return $content;
    }

    /**
     * Returns the content of the "<cellStyles>" section.
     *
     * @return string
     */
    protected function getCellStylesSectionContent()
    {
        return <<<EOD
<cellStyles count="1">
    <cellStyle builtinId="0" name="Normal" xfId="0"/>
</cellStyles>
EOD;
    }
}
