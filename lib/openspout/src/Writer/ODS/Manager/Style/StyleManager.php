<?php

namespace OpenSpout\Writer\ODS\Manager\Style;

use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Writer\Common\Entity\Options;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Manager\ManagesCellSize;
use OpenSpout\Writer\ODS\Helper\BorderHelper;

/**
 * Manages styles to be applied to a cell.
 */
class StyleManager extends \OpenSpout\Writer\Common\Manager\Style\StyleManager
{
    use ManagesCellSize;

    /** @var StyleRegistry */
    protected $styleRegistry;

    public function __construct(StyleRegistry $styleRegistry, OptionsManagerInterface $optionsManager)
    {
        parent::__construct($styleRegistry);
        $this->setDefaultColumnWidth($optionsManager->getOption(Options::DEFAULT_COLUMN_WIDTH));
        $this->setDefaultRowHeight($optionsManager->getOption(Options::DEFAULT_ROW_HEIGHT));
        $this->columnWidths = $optionsManager->getOption(Options::COLUMN_WIDTHS) ?? [];
    }

    /**
     * Returns the content of the "styles.xml" file, given a list of styles.
     *
     * @param int $numWorksheets Number of worksheets created
     *
     * @return string
     */
    public function getStylesXMLFileContent($numWorksheets)
    {
        $content = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <office:document-styles office:version="1.2" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:msoxl="http://schemas.microsoft.com/office/excel/formula" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
            EOD;

        $content .= $this->getFontFaceSectionContent();
        $content .= $this->getStylesSectionContent();
        $content .= $this->getAutomaticStylesSectionContent($numWorksheets);
        $content .= $this->getMasterStylesSectionContent($numWorksheets);

        $content .= <<<'EOD'
            </office:document-styles>
            EOD;

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
        foreach ($this->styleRegistry->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="'.$fontName.'" svg:font-family="'.$fontName.'"/>';
        }
        $content .= '</office:font-face-decls>';

        return $content;
    }

    /**
     * Returns the contents of the "<office:automatic-styles>" section, inside "content.xml" file.
     *
     * @param Worksheet[] $worksheets
     *
     * @return string
     */
    public function getContentXmlAutomaticStylesSectionContent($worksheets)
    {
        $content = '<office:automatic-styles>';

        foreach ($this->styleRegistry->getRegisteredStyles() as $style) {
            $content .= $this->getStyleSectionContent($style);
        }

        $useOptimalRowHeight = empty($this->defaultRowHeight) ? 'true' : 'false';
        $defaultRowHeight = empty($this->defaultRowHeight) ? '15pt' : "{$this->defaultRowHeight}pt";
        $defaultColumnWidth = empty($this->defaultColumnWidth) ? '' : "style:column-width=\"{$this->defaultColumnWidth}pt\"";

        $content .= <<<EOD
            <style:style style:family="table-column" style:name="default-column-style">
                <style:table-column-properties fo:break-before="auto" {$defaultColumnWidth}/>
            </style:style>
            <style:style style:family="table-row" style:name="ro1">
                <style:table-row-properties fo:break-before="auto" style:row-height="{$defaultRowHeight}" style:use-optimal-row-height="{$useOptimalRowHeight}"/>
            </style:style>
            EOD;

        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $isSheetVisible = $worksheet->getExternalSheet()->isVisible() ? 'true' : 'false';

            $content .= <<<EOD
                <style:style style:family="table" style:master-page-name="mp{$worksheetId}" style:name="ta{$worksheetId}">
                    <style:table-properties style:writing-mode="lr-tb" table:display="{$isSheetVisible}"/>
                </style:style>
                EOD;
        }

        // Sort column widths since ODS cares about order
        usort($this->columnWidths, function ($a, $b) {
            if ($a[0] === $b[0]) {
                return 0;
            }

            return ($a[0] < $b[0]) ? -1 : 1;
        });
        $content .= $this->getTableColumnStylesXMLContent();

        $content .= '</office:automatic-styles>';

        return $content;
    }

    public function getTableColumnStylesXMLContent(): string
    {
        if (empty($this->columnWidths)) {
            return '';
        }

        $content = '';
        foreach ($this->columnWidths as $styleIndex => $entry) {
            $content .= <<<EOD
                <style:style style:family="table-column" style:name="co{$styleIndex}">
                    <style:table-column-properties fo:break-before="auto" style:use-optimal-column-width="false" style:column-width="{$entry[2]}pt"/>
                </style:style>
                EOD;
        }

        return $content;
    }

    public function getStyledTableColumnXMLContent(int $maxNumColumns): string
    {
        if (empty($this->columnWidths)) {
            return '';
        }

        $content = '';
        foreach ($this->columnWidths as $styleIndex => $entry) {
            $numCols = $entry[1] - $entry[0] + 1;
            $content .= <<<EOD
                <table:table-column table:default-cell-style-name='Default' table:style-name="co{$styleIndex}" table:number-columns-repeated="{$numCols}"/>
                EOD;
        }
        // Note: This assumes the column widths are contiguous and default width is
        // only applied to columns after the last custom column with a custom width
        $content .= '<table:table-column table:default-cell-style-name="ce1" table:style-name="default-column-style" table:number-columns-repeated="'.($maxNumColumns - $entry[1]).'"/>';

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
        foreach ($this->styleRegistry->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="'.$fontName.'" svg:font-family="'.$fontName.'"/>';
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
     *
     * @return string
     */
    protected function getAutomaticStylesSectionContent($numWorksheets)
    {
        $content = '<office:automatic-styles>';

        for ($i = 1; $i <= $numWorksheets; ++$i) {
            $content .= <<<EOD
                <style:page-layout style:name="pm{$i}">
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
     *
     * @return string
     */
    protected function getMasterStylesSectionContent($numWorksheets)
    {
        $content = '<office:master-styles>';

        for ($i = 1; $i <= $numWorksheets; ++$i) {
            $content .= <<<EOD
                <style:master-page style:name="mp{$i}" style:page-layout-name="pm{$i}">
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
     * Returns the contents of the "<style:style>" section, inside "<office:automatic-styles>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    protected function getStyleSectionContent($style)
    {
        $styleIndex = $style->getId() + 1; // 1-based

        $content = '<style:style style:data-style-name="N0" style:family="table-cell" style:name="ce'.$styleIndex.'" style:parent-style-name="Default">';

        $content .= $this->getTextPropertiesSectionContent($style);
        $content .= $this->getParagraphPropertiesSectionContent($style);
        $content .= $this->getTableCellPropertiesSectionContent($style);

        $content .= '</style:style>';

        return $content;
    }

    /**
     * Returns the contents of the "<style:text-properties>" section, inside "<style:style>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getTextPropertiesSectionContent($style)
    {
        if (!$style->shouldApplyFont()) {
            return '';
        }

        return '<style:text-properties '
            .$this->getFontSectionContent($style)
            .'/>';
    }

    /**
     * Returns the contents of the fonts definition section, inside "<style:text-properties>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getFontSectionContent($style)
    {
        $defaultStyle = $this->getDefaultStyle();
        $content = '';

        $fontColor = $style->getFontColor();
        if ($fontColor !== $defaultStyle->getFontColor()) {
            $content .= ' fo:color="#'.$fontColor.'"';
        }

        $fontName = $style->getFontName();
        if ($fontName !== $defaultStyle->getFontName()) {
            $content .= ' style:font-name="'.$fontName.'" style:font-name-asian="'.$fontName.'" style:font-name-complex="'.$fontName.'"';
        }

        $fontSize = $style->getFontSize();
        if ($fontSize !== $defaultStyle->getFontSize()) {
            $content .= ' fo:font-size="'.$fontSize.'pt" style:font-size-asian="'.$fontSize.'pt" style:font-size-complex="'.$fontSize.'pt"';
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

        return $content;
    }

    /**
     * Returns the contents of the "<style:paragraph-properties>" section, inside "<style:style>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getParagraphPropertiesSectionContent($style)
    {
        if (!$style->shouldApplyCellAlignment()) {
            return '';
        }

        return '<style:paragraph-properties '
            .$this->getCellAlignmentSectionContent($style)
            .'/>';
    }

    /**
     * Returns the contents of the cell alignment definition for the "<style:paragraph-properties>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getCellAlignmentSectionContent($style)
    {
        return sprintf(
            ' fo:text-align="%s" ',
            $this->transformCellAlignment($style->getCellAlignment())
        );
    }

    /**
     * Even though "left" and "right" alignments are part of the spec, and interpreted
     * respectively as "start" and "end", using the recommended values increase compatibility
     * with software that will read the created ODS file.
     *
     * @param string $cellAlignment
     *
     * @return string
     */
    private function transformCellAlignment($cellAlignment)
    {
        switch ($cellAlignment) {
            case CellAlignment::LEFT:
                return 'start';

            case CellAlignment::RIGHT:
                return 'end';

            default:
                return $cellAlignment;
        }
    }

    /**
     * Returns the contents of the "<style:table-cell-properties>" section, inside "<style:style>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getTableCellPropertiesSectionContent($style)
    {
        $content = '<style:table-cell-properties ';

        if ($style->shouldWrapText()) {
            $content .= $this->getWrapTextXMLContent();
        }

        if ($style->shouldApplyBorder()) {
            $content .= $this->getBorderXMLContent($style);
        }

        if ($style->shouldApplyBackgroundColor()) {
            $content .= $this->getBackgroundColorXMLContent($style);
        }

        $content .= '/>';

        return $content;
    }

    /**
     * Returns the contents of the wrap text definition for the "<style:table-cell-properties>" section.
     *
     * @return string
     */
    private function getWrapTextXMLContent()
    {
        return ' fo:wrap-option="wrap" style:vertical-align="automatic" ';
    }

    /**
     * Returns the contents of the borders definition for the "<style:table-cell-properties>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getBorderXMLContent($style)
    {
        $borders = array_map(function (BorderPart $borderPart) {
            return BorderHelper::serializeBorderPart($borderPart);
        }, $style->getBorder()->getParts());

        return sprintf(' %s ', implode(' ', $borders));
    }

    /**
     * Returns the contents of the background color definition for the "<style:table-cell-properties>" section.
     *
     * @param \OpenSpout\Common\Entity\Style\Style $style
     *
     * @return string
     */
    private function getBackgroundColorXMLContent($style)
    {
        return sprintf(' fo:background-color="#%s" ', $style->getBackgroundColor());
    }
}
