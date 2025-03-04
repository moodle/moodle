<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Manager\Style;

use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\AbstractOptions;
use OpenSpout\Writer\Common\ColumnWidth;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Manager\Style\AbstractStyleManager as CommonStyleManager;
use OpenSpout\Writer\ODS\Helper\BorderHelper;

/**
 * @internal
 *
 * @property StyleRegistry $styleRegistry
 */
final class StyleManager extends CommonStyleManager
{
    private readonly AbstractOptions $options;

    public function __construct(StyleRegistry $styleRegistry, AbstractOptions $options)
    {
        parent::__construct($styleRegistry);
        $this->options = $options;
    }

    /**
     * Returns the content of the "styles.xml" file, given a list of styles.
     *
     * @param int $numWorksheets Number of worksheets created
     */
    public function getStylesXMLFileContent(int $numWorksheets): string
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
     */
    public function getContentXmlFontFaceSectionContent(): string
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
     */
    public function getContentXmlAutomaticStylesSectionContent(array $worksheets): string
    {
        $content = '<office:automatic-styles>';

        foreach ($this->styleRegistry->getRegisteredStyles() as $style) {
            $content .= $this->getStyleSectionContent($style);
        }

        $useOptimalRowHeight = null === $this->options->DEFAULT_ROW_HEIGHT ? 'true' : 'false';
        $defaultRowHeight = null === $this->options->DEFAULT_ROW_HEIGHT ? '15pt' : "{$this->options->DEFAULT_ROW_HEIGHT}pt";
        $defaultColumnWidth = null === $this->options->DEFAULT_COLUMN_WIDTH ? '' : "style:column-width=\"{$this->options->DEFAULT_COLUMN_WIDTH}pt\"";

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
        $columnWidths = $this->options->getColumnWidths();
        usort($columnWidths, static function (ColumnWidth $a, ColumnWidth $b): int {
            return $a->start <=> $b->start;
        });
        $content .= $this->getTableColumnStylesXMLContent();

        $content .= '</office:automatic-styles>';

        return $content;
    }

    public function getTableColumnStylesXMLContent(): string
    {
        if ([] === $this->options->getColumnWidths()) {
            return '';
        }

        $content = '';
        foreach ($this->options->getColumnWidths() as $styleIndex => $columnWidth) {
            $content .= <<<EOD
                <style:style style:family="table-column" style:name="co{$styleIndex}">
                    <style:table-column-properties fo:break-before="auto" style:use-optimal-column-width="false" style:column-width="{$columnWidth->width}pt"/>
                </style:style>
                EOD;
        }

        return $content;
    }

    public function getStyledTableColumnXMLContent(int $maxNumColumns): string
    {
        if ([] === $this->options->getColumnWidths()) {
            return '';
        }

        $content = '';
        foreach ($this->options->getColumnWidths() as $styleIndex => $columnWidth) {
            $numCols = $columnWidth->end - $columnWidth->start + 1;
            $content .= <<<EOD
                <table:table-column table:default-cell-style-name='Default' table:style-name="co{$styleIndex}" table:number-columns-repeated="{$numCols}"/>
                EOD;
        }
        \assert(isset($columnWidth));
        // Note: This assumes the column widths are contiguous and default width is
        // only applied to columns after the last custom column with a custom width
        $content .= '<table:table-column table:default-cell-style-name="ce1" table:style-name="default-column-style" table:number-columns-repeated="'.($maxNumColumns - $columnWidth->end).'"/>';

        return $content;
    }

    /**
     * Returns the content of the "<office:styles>" section, inside "styles.xml" file.
     */
    private function getStylesSectionContent(): string
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
     * Returns the content of the "<office:master-styles>" section, inside "styles.xml" file.
     *
     * @param int $numWorksheets Number of worksheets created
     */
    private function getMasterStylesSectionContent(int $numWorksheets): string
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
     * Returns the content of the "<office:font-face-decls>" section, inside "styles.xml" file.
     */
    private function getFontFaceSectionContent(): string
    {
        $content = '<office:font-face-decls>';
        foreach ($this->styleRegistry->getUsedFonts() as $fontName) {
            $content .= '<style:font-face style:name="'.$fontName.'" svg:font-family="'.$fontName.'"/>';
        }
        $content .= '</office:font-face-decls>';

        return $content;
    }

    /**
     * Returns the content of the "<office:automatic-styles>" section, inside "styles.xml" file.
     *
     * @param int $numWorksheets Number of worksheets created
     */
    private function getAutomaticStylesSectionContent(int $numWorksheets): string
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
     * Returns the contents of the "<style:style>" section, inside "<office:automatic-styles>" section.
     */
    private function getStyleSectionContent(Style $style): string
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
     */
    private function getTextPropertiesSectionContent(Style $style): string
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
     */
    private function getFontSectionContent(Style $style): string
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
     */
    private function getParagraphPropertiesSectionContent(Style $style): string
    {
        if (!$style->shouldApplyCellAlignment() && !$style->shouldApplyCellVerticalAlignment()) {
            return '';
        }

        return '<style:paragraph-properties '
            .$this->getCellAlignmentSectionContent($style)
            .$this->getCellVerticalAlignmentSectionContent($style)
            .'/>';
    }

    /**
     * Returns the contents of the cell alignment definition for the "<style:paragraph-properties>" section.
     */
    private function getCellAlignmentSectionContent(Style $style): string
    {
        if (!$style->hasSetCellAlignment()) {
            return '';
        }

        return \sprintf(
            ' fo:text-align="%s" ',
            $this->transformCellAlignment($style->getCellAlignment())
        );
    }

    /**
     * Returns the contents of the cell vertical alignment definition for the "<style:paragraph-properties>" section.
     */
    private function getCellVerticalAlignmentSectionContent(Style $style): string
    {
        if (!$style->hasSetCellVerticalAlignment()) {
            return '';
        }

        return \sprintf(
            ' fo:vertical-align="%s" ',
            $this->transformCellVerticalAlignment($style->getCellVerticalAlignment())
        );
    }

    /**
     * Even though "left" and "right" alignments are part of the spec, and interpreted
     * respectively as "start" and "end", using the recommended values increase compatibility
     * with software that will read the created ODS file.
     */
    private function transformCellAlignment(string $cellAlignment): string
    {
        return match ($cellAlignment) {
            CellAlignment::LEFT => 'start',
            CellAlignment::RIGHT => 'end',
            default => $cellAlignment,
        };
    }

    /**
     * Spec uses 'middle' rather than 'center'
     * http://docs.oasis-open.org/office/v1.2/os/OpenDocument-v1.2-os-part1.html#__RefHeading__1420236_253892949.
     */
    private function transformCellVerticalAlignment(string $cellVerticalAlignment): string
    {
        return (CellVerticalAlignment::CENTER === $cellVerticalAlignment)
            ? 'middle'
            : $cellVerticalAlignment;
    }

    /**
     * Returns the contents of the "<style:table-cell-properties>" section, inside "<style:style>" section.
     */
    private function getTableCellPropertiesSectionContent(Style $style): string
    {
        $content = '<style:table-cell-properties ';

        if ($style->hasSetWrapText()) {
            $content .= $this->getWrapTextXMLContent($style->shouldWrapText());
        }

        if (null !== ($border = $style->getBorder())) {
            $content .= $this->getBorderXMLContent($border);
        }

        if (null !== ($bgColor = $style->getBackgroundColor())) {
            $content .= $this->getBackgroundColorXMLContent($bgColor);
        }

        $content .= '/>';

        return $content;
    }

    /**
     * Returns the contents of the wrap text definition for the "<style:table-cell-properties>" section.
     */
    private function getWrapTextXMLContent(bool $shouldWrapText): string
    {
        return ' fo:wrap-option="'.($shouldWrapText ? '' : 'no-').'wrap" style:vertical-align="automatic" ';
    }

    /**
     * Returns the contents of the borders definition for the "<style:table-cell-properties>" section.
     */
    private function getBorderXMLContent(Border $border): string
    {
        $borders = array_map(static function (BorderPart $borderPart) {
            return BorderHelper::serializeBorderPart($borderPart);
        }, $border->getParts());

        return \sprintf(' %s ', implode(' ', $borders));
    }

    /**
     * Returns the contents of the background color definition for the "<style:table-cell-properties>" section.
     */
    private function getBackgroundColorXMLContent(string $bgColor): string
    {
        return \sprintf(' fo:background-color="#%s" ', $bgColor);
    }
}
