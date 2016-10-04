<?php

namespace Box\Spout\Writer\ODS\Helper;

use Box\Spout\Writer\Common\Helper\AbstractStyleHelper;
use Box\Spout\Writer\Style\BorderPart;

/**
 * Class StyleHelper
 * This class provides helper functions to manage styles
 *
 * @package Box\Spout\Writer\ODS\Helper
 */
class StyleHelper extends AbstractStyleHelper
{
    /** @var string[] [FONT_NAME] => [] Map whose keys contain all the fonts used */
    protected $usedFontsSet = [];

    /**
     * Registers the given style as a used style.
     * Duplicate styles won't be registered more than once.
     *
     * @param \Box\Spout\Writer\Style\Style $style The style to be registered
     * @return \Box\Spout\Writer\Style\Style The registered style, updated with an internal ID.
     */
    public function registerStyle($style)
    {
        $this->usedFontsSet[$style->getFontName()] = true;
        return parent::registerStyle($style);
    }

    /**
     * @return string[] List of used fonts name
     */
    protected function getUsedFonts()
    {
        return array_keys($this->usedFontsSet);
    }

    /**
     * Returns the content of the "styles.xml" file, given a list of styles.
     *
     * @param int $numWorksheets Number of worksheets created
     * @return string
     */
    public function getStylesXMLFileContent($numWorksheets)
    {
        $content = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<office:document-styles office:version="1.2" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:msoxl="http://schemas.microsoft.com/office/excel/formula" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
EOD;

        $content .= $this->getFontFaceSectionContent();
        $content .= $this->getStylesSectionContent();
        $content .= $this->getAutomaticStylesSectionContent($numWorksheets);
        $content .= $this->getMasterStylesSectionContent($numWorksheets);

        $content .= <<<EOD
</office:document-styles>
EOD;

        return $content;
    }

    /**
     * Returns the content of the "<office:font-face-decls>" section, inside "styles.xml" file.
     *
     * @return string
     */
    protected function getFontFaceSectionContent()
    {
        $content = '<office:font-face-decls>';
        foreach ($this->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="' . $fontName . '" svg:font-family="' . $fontName . '"/>';
        }
        $content .= '</office:font-face-decls>';

        return $content;
    }

    /**
     * Returns the content of the "<office:styles>" section, inside "styles.xml" file.
     *
     * @return string
     */
    protected function getStylesSectionContent()
    {
        $defaultStyle = $this->getDefaultStyle();

        return <<<EOD
<office:styles>
    <number:number-style style:name="N0">
        <number:number number:min-integer-digits="1"/>
    </number:number-style>
    <style:style style:data-style-name="N0" style:family="table-cell" style:name="Default">
        <style:table-cell-properties fo:background-color="transparent" style:vertical-align="automatic"/>
        <style:text-properties fo:color="#{$defaultStyle->getFontColor()}"
                               fo:font-size="{$defaultStyle->getFontSize()}pt" style:font-size-asian="{$defaultStyle->getFontSize()}pt" style:font-size-complex="{$defaultStyle->getFontSize()}pt"
                               style:font-name="{$defaultStyle->getFontName()}" style:font-name-asian="{$defaultStyle->getFontName()}" style:font-name-complex="{$defaultStyle->getFontName()}"/>
    </style:style>
</office:styles>
EOD;
    }

    /**
     * Returns the content of the "<office:automatic-styles>" section, inside "styles.xml" file.
     *
     * @param int $numWorksheets Number of worksheets created
     * @return string
     */
    protected function getAutomaticStylesSectionContent($numWorksheets)
    {
        $content = '<office:automatic-styles>';

        for ($i = 1; $i <= $numWorksheets; $i++) {
            $content .= <<<EOD
<style:page-layout style:name="pm$i">
    <style:page-layout-properties style:first-page-number="continue" style:print="objects charts drawings" style:table-centering="none"/>
    <style:header-style/>
    <style:footer-style/>
</style:page-layout>
EOD;
        }

        $content .= '</office:automatic-styles>';

        return $content;
    }

    /**
     * Returns the content of the "<office:master-styles>" section, inside "styles.xml" file.
     *
     * @param int $numWorksheets Number of worksheets created
     * @return string
     */
    protected function getMasterStylesSectionContent($numWorksheets)
    {
        $content = '<office:master-styles>';

        for ($i = 1; $i <= $numWorksheets; $i++) {
            $content .= <<<EOD
<style:master-page style:name="mp$i" style:page-layout-name="pm$i">
    <style:header/>
    <style:header-left style:display="false"/>
    <style:footer/>
    <style:footer-left style:display="false"/>
</style:master-page>
EOD;
        }

        $content .= '</office:master-styles>';

        return $content;
    }


    /**
     * Returns the contents of the "<office:font-face-decls>" section, inside "content.xml" file.
     *
     * @return string
     */
    public function getContentXmlFontFaceSectionContent()
    {
        $content = '<office:font-face-decls>';
        foreach ($this->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="' . $fontName . '" svg:font-family="' . $fontName . '"/>';
        }
        $content .= '</office:font-face-decls>';

        return $content;
    }

    /**
     * Returns the contents of the "<office:automatic-styles>" section, inside "content.xml" file.
     *
     * @param int $numWorksheets Number of worksheets created
     * @return string
     */
    public function getContentXmlAutomaticStylesSectionContent($numWorksheets)
    {
        $content = '<office:automatic-styles>';

        foreach ($this->getRegisteredStyles() as $style) {
            $content .= $this->getStyleSectionContent($style);
        }

        $content .= <<<EOD
<style:style style:family="table-column" style:name="co1">
    <style:table-column-properties fo:break-before="auto"/>
</style:style>
<style:style style:family="table-row" style:name="ro1">
    <style:table-row-properties fo:break-before="auto" style:row-height="15pt" style:use-optimal-row-height="true"/>
</style:style>
EOD;

        for ($i = 1; $i <= $numWorksheets; $i++) {
            $content .= <<<EOD
<style:style style:family="table" style:master-page-name="mp$i" style:name="ta$i">
    <style:table-properties style:writing-mode="lr-tb" table:display="true"/>
</style:style>
EOD;
        }

        $content .= '</office:automatic-styles>';

        return $content;
    }

    /**
     * Returns the contents of the "<style:style>" section, inside "<office:automatic-styles>" section
     *
     * @param \Box\Spout\Writer\Style\Style $style
     * @return string
     */
    protected function getStyleSectionContent($style)
    {
        $defaultStyle = $this->getDefaultStyle();
        $styleIndex = $style->getId() + 1; // 1-based

        $content = '<style:style style:data-style-name="N0" style:family="table-cell" style:name="ce' . $styleIndex . '" style:parent-style-name="Default">';

        if ($style->shouldApplyFont()) {
            $content .= '<style:text-properties';

            $fontColor = $style->getFontColor();
            if ($fontColor !== $defaultStyle->getFontColor()) {
                $content .= ' fo:color="#' . $fontColor . '"';
            }

            $fontName = $style->getFontName();
            if ($fontName !== $defaultStyle->getFontName()) {
                $content .= ' style:font-name="' . $fontName . '" style:font-name-asian="' . $fontName . '" style:font-name-complex="' . $fontName . '"';
            }

            $fontSize = $style->getFontSize();
            if ($fontSize !== $defaultStyle->getFontSize()) {
                $content .= ' fo:font-size="' . $fontSize . 'pt" style:font-size-asian="' . $fontSize . 'pt" style:font-size-complex="' . $fontSize . 'pt"';
            }

            if ($style->isFontBold()) {
                $content .= ' fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"';
            }
            if ($style->isFontItalic()) {
                $content .= ' fo:font-style="italic" style:font-style-asian="italic" style:font-style-complex="italic"';
            }
            if ($style->isFontUnderline()) {
                $content .= ' style:text-underline-style="solid" style:text-underline-type="single"';
            }
            if ($style->isFontStrikethrough()) {
                $content .= ' style:text-line-through-style="solid"';
            }

            $content .= '/>';
        }

        if ($style->shouldWrapText()) {
            $content .= '<style:table-cell-properties fo:wrap-option="wrap" style:vertical-align="automatic"/>';
        }

        if ($style->shouldApplyBorder()) {
            $borderProperty = '<style:table-cell-properties %s />';
            $borders = array_map(function (BorderPart $borderPart) {
                return BorderHelper::serializeBorderPart($borderPart);
            }, $style->getBorder()->getParts());
            $content .= sprintf($borderProperty, implode(' ', $borders));
        }

        if ($style->shouldApplyBackgroundColor()) {
            $content .= sprintf('
                <style:table-cell-properties fo:background-color="#%s"/>', $style->getBackgroundColor());
        }

        $content .= '</style:style>';

        return $content;
    }
}
