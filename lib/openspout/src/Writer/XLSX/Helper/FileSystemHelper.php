<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Helper;

use DateTimeImmutable;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Common\Helper\FileSystemHelper as CommonFileSystemHelper;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;
use OpenSpout\Writer\XLSX\MergeCell;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Properties;

/**
 * @internal
 */
final class FileSystemHelper implements FileSystemWithRootFolderHelperInterface
{
    public const RELS_FOLDER_NAME = '_rels';
    public const DRAWINGS_FOLDER_NAME = 'drawings';
    public const DOC_PROPS_FOLDER_NAME = 'docProps';
    public const XL_FOLDER_NAME = 'xl';
    public const WORKSHEETS_FOLDER_NAME = 'worksheets';

    public const RELS_FILE_NAME = '.rels';
    public const APP_XML_FILE_NAME = 'app.xml';
    public const CORE_XML_FILE_NAME = 'core.xml';
    public const CUSTOM_XML_FILE_NAME = 'custom.xml';
    public const CONTENT_TYPES_XML_FILE_NAME = '[Content_Types].xml';
    public const WORKBOOK_XML_FILE_NAME = 'workbook.xml';
    public const WORKBOOK_RELS_XML_FILE_NAME = 'workbook.xml.rels';
    public const STYLES_XML_FILE_NAME = 'styles.xml';

    private const SHEET_XML_FILE_HEADER = <<<'EOD'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
        EOD;

    private readonly string $baseFolderRealPath;
    private readonly CommonFileSystemHelper $baseFileSystemHelper;

    /** @var ZipHelper Helper to perform tasks with Zip archive */
    private readonly ZipHelper $zipHelper;

    /** @var Properties document properties */
    private readonly Properties $properties;

    /** @var XLSX Used to escape XML data */
    private readonly XLSX $escaper;

    /** @var string Path to the root folder inside the temp folder where the files to create the XLSX will be stored */
    private string $rootFolder;

    /** @var string Path to the "_rels" folder inside the root folder */
    private string $relsFolder;

    /** @var string Path to the "docProps" folder inside the root folder */
    private string $docPropsFolder;

    /** @var string Path to the "xl" folder inside the root folder */
    private string $xlFolder;

    /** @var string Path to the "_rels" folder inside the "xl" folder */
    private string $xlRelsFolder;

    /** @var string Path to the "worksheets" folder inside the "xl" folder */
    private string $xlWorksheetsFolder;

    /** @var string Path to the temp folder, inside the root folder, where specific sheets content will be written to */
    private string $sheetsContentTempFolder;

    /**
     * @param string     $baseFolderPath The path of the base folder where all the I/O can occur
     * @param ZipHelper  $zipHelper      Helper to perform tasks with Zip archive
     * @param XLSX       $escaper        Used to escape XML data
     * @param Properties $properties     document properies
     */
    public function __construct(string $baseFolderPath, ZipHelper $zipHelper, XLSX $escaper, Properties $properties)
    {
        $this->baseFileSystemHelper = new CommonFileSystemHelper($baseFolderPath);
        $this->baseFolderRealPath = $this->baseFileSystemHelper->getBaseFolderRealPath();
        $this->zipHelper = $zipHelper;
        $this->escaper = $escaper;
        $this->properties = $properties;
    }

    public function createFolder(string $parentFolderPath, string $folderName): string
    {
        return $this->baseFileSystemHelper->createFolder($parentFolderPath, $folderName);
    }

    public function createFileWithContents(string $parentFolderPath, string $fileName, string $fileContents): string
    {
        return $this->baseFileSystemHelper->createFileWithContents($parentFolderPath, $fileName, $fileContents);
    }

    public function deleteFile(string $filePath): void
    {
        $this->baseFileSystemHelper->deleteFile($filePath);
    }

    public function deleteFolderRecursively(string $folderPath): void
    {
        $this->baseFileSystemHelper->deleteFolderRecursively($folderPath);
    }

    public function getRootFolder(): string
    {
        return $this->rootFolder;
    }

    public function getXlFolder(): string
    {
        return $this->xlFolder;
    }

    public function getXlWorksheetsFolder(): string
    {
        return $this->xlWorksheetsFolder;
    }

    public function getSheetsContentTempFolder(): string
    {
        return $this->sheetsContentTempFolder;
    }

    /**
     * Creates all the folders needed to create a XLSX file, as well as the files that won't change.
     *
     * @throws IOException If unable to create at least one of the base folders
     */
    public function createBaseFilesAndFolders(): void
    {
        $this
            ->createRootFolder()
            ->createRelsFolderAndFile()
            ->createDocPropsFolderAndFiles()
            ->createXlFolderAndSubFolders()
            ->createSheetsContentTempFolder()
        ;
    }

    /**
     * Creates the "[Content_Types].xml" file under the root folder.
     *
     * @param Worksheet[] $worksheets
     */
    public function createContentTypesFile(array $worksheets): self
    {
        $contentTypesXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default ContentType="application/xml" Extension="xml"/>
                <Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/>
                <Default ContentType="application/vnd.openxmlformats-officedocument.vmlDrawing" Extension="vml"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/>
            EOD;

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $contentTypesXmlFileContents .= '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet'.$worksheet->getId().'.xml"/>';
            $contentTypesXmlFileContents .= '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.comments+xml" PartName="/xl/comments'.$worksheet->getId().'.xml" />';
        }

        $contentTypesXmlFileContents .= <<<'EOD'
            <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" PartName="/xl/styles.xml"/>
            <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml" PartName="/xl/sharedStrings.xml"/>
            <Override ContentType="application/vnd.openxmlformats-package.core-properties+xml" PartName="/docProps/core.xml"/>
            <Override ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" PartName="/docProps/app.xml"/>
            EOD;

        if ([] !== $this->properties->customProperties) {
            $contentTypesXmlFileContents .= <<<'EOD'
                <Override ContentType="application/vnd.openxmlformats-officedocument.custom-properties+xml" PartName="/docProps/custom.xml" />
                EOD;
        }

        $contentTypesXmlFileContents .= <<<'EOD'
            </Types>
            EOD;

        $this->createFileWithContents($this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME, $contentTypesXmlFileContents);

        return $this;
    }

    /**
     * Creates the "workbook.xml" file under the "xl" folder.
     *
     * @param Worksheet[] $worksheets
     */
    public function createWorkbookFile(Options $options, array $worksheets): self
    {
        $workbookXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
            EOD;

        if (null !== $options->getWorkbookProtection()) {
            $workbookXmlFileContents .= $options->getWorkbookProtection()->getXml();
        }

        $workbookXmlFileContents .= <<<'EOD'
                <sheets>
            EOD;

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $worksheetName = $worksheet->getExternalSheet()->getName();
            $worksheetVisibility = $worksheet->getExternalSheet()->isVisible() ? 'visible' : 'hidden';
            $worksheetId = $worksheet->getId();
            $workbookXmlFileContents .= '<sheet name="'.$this->escaper->escape($worksheetName).'" sheetId="'.$worksheetId.'" r:id="rIdSheet'.$worksheetId.'" state="'.$worksheetVisibility.'"/>';
        }

        $workbookXmlFileContents .= <<<'EOD'
                </sheets>
            EOD;

        $definedNames = '';

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $sheet = $worksheet->getExternalSheet();
            if (null !== $autofilter = $sheet->getAutoFilter()) {
                $worksheetName = $sheet->getName();
                $name = \sprintf(
                    '\'%s\'!$%s$%s:$%s$%s',
                    $this->escaper->escape($worksheetName),
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->fromColumnIndex),
                    $autofilter->fromRow,
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->toColumnIndex),
                    $autofilter->toRow
                );
                $definedNames .= '<definedName function="false" hidden="true" localSheetId="'.$sheet->getIndex().'" name="_xlnm._FilterDatabase" vbProcedure="false">'.$name.'</definedName>';
            }
            if (null !== $printTitleRows = $sheet->getPrintTitleRows()) {
                $definedNames .= '<definedName name="_xlnm.Print_Titles" localSheetId="'.$sheet->getIndex().'">'.$this->escaper->escape($sheet->getName()).'!'.$printTitleRows.'</definedName>';
            }
        }
        if ('' !== $definedNames) {
            $workbookXmlFileContents .= '<definedNames>'.$definedNames.'</definedNames>';
        }

        $workbookXmlFileContents .= <<<'EOD'
            </workbook>
            EOD;

        $this->createFileWithContents($this->xlFolder, self::WORKBOOK_XML_FILE_NAME, $workbookXmlFileContents);

        return $this;
    }

    /**
     * Creates the "workbook.xml.res" file under the "xl/_res" folder.
     *
     * @param Worksheet[] $worksheets
     */
    public function createWorkbookRelsFile(array $worksheets): self
    {
        $workbookRelsXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rIdStyles" Target="styles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/>
                <Relationship Id="rIdSharedStrings" Target="sharedStrings.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"/>
            EOD;

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $workbookRelsXmlFileContents .= '<Relationship Id="rIdSheet'.$worksheetId.'" Target="worksheets/sheet'.$worksheetId.'.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>';
        }

        $workbookRelsXmlFileContents .= '</Relationships>';

        $this->createFileWithContents($this->xlRelsFolder, self::WORKBOOK_RELS_XML_FILE_NAME, $workbookRelsXmlFileContents);

        return $this;
    }

    /**
     * Create the "rels" file for a given worksheet. This contains relations to the comments.xml and drawing.vml files for this worksheet.
     *
     * @param Worksheet[] $worksheets
     */
    public function createWorksheetRelsFiles(array $worksheets): self
    {
        $this->createFolder($this->getXlWorksheetsFolder(), self::RELS_FOLDER_NAME);

        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $worksheetRelsContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
              <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId_comments_vml1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing" Target="../drawings/vmlDrawing'.$worksheetId.'.vml"/>
                <Relationship Id="rId_comments1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments" Target="../comments'.$worksheetId.'.xml"/>
              </Relationships>';

            $folder = $this->getXlWorksheetsFolder().\DIRECTORY_SEPARATOR.'_rels';
            $filename = 'sheet'.$worksheetId.'.xml.rels';

            $this->createFileWithContents($folder, $filename, $worksheetRelsContent);
        }

        return $this;
    }

    /**
     * Creates the "styles.xml" file under the "xl" folder.
     */
    public function createStylesFile(StyleManager $styleManager): self
    {
        $stylesXmlFileContents = $styleManager->getStylesXMLFileContent();
        $this->createFileWithContents($this->xlFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

        return $this;
    }

    /**
     * Creates the "content.xml" file under the root folder.
     *
     * @param Worksheet[] $worksheets
     */
    public function createContentFiles(Options $options, array $worksheets): self
    {
        $allMergeCells = $options->getMergeCells();
        $pageSetup = $options->getPageSetup();
        foreach ($worksheets as $worksheet) {
            $contentXmlFilePath = $this->getXlWorksheetsFolder().\DIRECTORY_SEPARATOR.basename($worksheet->getFilePath());
            $worksheetFilePointer = fopen($contentXmlFilePath, 'w');
            \assert(false !== $worksheetFilePointer);

            $sheet = $worksheet->getExternalSheet();
            fwrite($worksheetFilePointer, self::SHEET_XML_FILE_HEADER);

            // AutoFilter tags
            if (null !== $autofilter = $sheet->getAutoFilter()) {
                if (isset($pageSetup) && $pageSetup->fitToPage) {
                    fwrite($worksheetFilePointer, '<sheetPr filterMode="false"><pageSetUpPr fitToPage="true"/></sheetPr>');
                } else {
                    fwrite($worksheetFilePointer, '<sheetPr filterMode="false"><pageSetUpPr fitToPage="false"/></sheetPr>');
                }
            } elseif (isset($pageSetup) && $pageSetup->fitToPage) {
                fwrite($worksheetFilePointer, '<sheetPr><pageSetUpPr fitToPage="true"/></sheetPr>');
            }
            $sheetRange = \sprintf(
                '%s%s:%s%s',
                CellHelper::getColumnLettersFromColumnIndex(0),
                1,
                CellHelper::getColumnLettersFromColumnIndex($worksheet->getMaxNumColumns() - 1),
                $worksheet->getLastWrittenRowIndex()
            );
            fwrite($worksheetFilePointer, \sprintf('<dimension ref="%s"/>', $sheetRange));
            if (null !== ($sheetView = $sheet->getSheetView())) {
                fwrite($worksheetFilePointer, '<sheetViews>'.$sheetView->getXml().'</sheetViews>');
            }
            fwrite($worksheetFilePointer, $this->getXMLFragmentForDefaultCellSizing($options));
            fwrite($worksheetFilePointer, $this->getXMLFragmentForColumnWidths($options, $sheet));
            fwrite($worksheetFilePointer, '<sheetData>');

            $worksheetFilePath = $worksheet->getFilePath();
            $this->copyFileContentsToTarget($worksheetFilePath, $worksheetFilePointer);
            fwrite($worksheetFilePointer, '</sheetData>');

            if (null !== $sheet->getSheetProtection()) {
                fwrite($worksheetFilePointer, $sheet->getSheetProtection()->getXml());
            }

            // AutoFilter tag
            if (null !== $autofilter) {
                $autoFilterRange = \sprintf(
                    '%s%s:%s%s',
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->fromColumnIndex),
                    $autofilter->fromRow,
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->toColumnIndex),
                    $autofilter->toRow
                );
                fwrite($worksheetFilePointer, \sprintf('<autoFilter ref="%s"/>', $autoFilterRange));
            }

            // create nodes for merge cells
            $mergeCells = array_filter(
                $allMergeCells,
                static fn (MergeCell $c) => $c->sheetIndex === $worksheet->getExternalSheet()->getIndex(),
            );
            if ([] !== $mergeCells) {
                $mergeCellString = '<mergeCells count="'.\count($mergeCells).'">';
                foreach ($mergeCells as $mergeCell) {
                    $topLeft = CellHelper::getColumnLettersFromColumnIndex($mergeCell->topLeftColumn).$mergeCell->topLeftRow;
                    $bottomRight = CellHelper::getColumnLettersFromColumnIndex($mergeCell->bottomRightColumn).$mergeCell->bottomRightRow;
                    $mergeCellString .= \sprintf(
                        '<mergeCell ref="%s:%s"/>',
                        $topLeft,
                        $bottomRight
                    );
                }
                $mergeCellString .= '</mergeCells>';
                fwrite($worksheetFilePointer, $mergeCellString);
            }

            $this->getXMLFragmentForPageMargin($worksheetFilePointer, $options);

            $this->getXMLFragmentForPageSetup($worksheetFilePointer, $options);

            $this->getXMLFragmentForHeaderFooter($worksheetFilePointer, $options);

            // Add the legacy drawing for comments
            fwrite($worksheetFilePointer, '<legacyDrawing r:id="rId_comments_vml1"/>');

            fwrite($worksheetFilePointer, '</worksheet>');
            fclose($worksheetFilePointer);
        }

        return $this;
    }

    /**
     * Deletes the temporary folder where sheets content was stored.
     */
    public function deleteWorksheetTempFolder(): self
    {
        $this->deleteFolderRecursively($this->sheetsContentTempFolder);

        return $this;
    }

    /**
     * Zips the root folder and streams the contents of the zip into the given stream.
     *
     * @param resource $streamPointer Pointer to the stream to copy the zip
     */
    public function zipRootFolderAndCopyToStream($streamPointer): void
    {
        $zip = $this->zipHelper->createZip($this->rootFolder);

        $zipFilePath = $this->zipHelper->getZipFilePath($zip);

        // In order to have the file's mime type detected properly, files need to be added
        // to the zip file in a particular order.
        // "[Content_Types].xml" then at least 2 files located in "xl" folder should be zipped first.
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME);
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::XL_FOLDER_NAME.\DIRECTORY_SEPARATOR.self::WORKBOOK_XML_FILE_NAME);
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::XL_FOLDER_NAME.\DIRECTORY_SEPARATOR.self::STYLES_XML_FILE_NAME);

        $this->zipHelper->addFolderToArchive($zip, $this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $this->zipHelper->closeArchiveAndCopyToStream($zip, $streamPointer);

        // once the zip is copied, remove it
        $this->deleteFile($zipFilePath);
    }

    /**
     * @param resource $targetResource
     */
    private function getXMLFragmentForPageMargin($targetResource, Options $options): void
    {
        $pageMargin = $options->getPageMargin();
        if (null === $pageMargin) {
            return;
        }

        fwrite($targetResource, "<pageMargins top=\"{$pageMargin->top}\" right=\"{$pageMargin->right}\" bottom=\"{$pageMargin->bottom}\" left=\"{$pageMargin->left}\" header=\"{$pageMargin->header}\" footer=\"{$pageMargin->footer}\"/>");
    }

    /**
     * @param resource $targetResource
     */
    private function getXMLFragmentForHeaderFooter($targetResource, Options $options): void
    {
        $headerFooter = $options->getHeaderFooter();
        if (null === $headerFooter) {
            return;
        }

        $xml = '<headerFooter';

        if ($headerFooter->differentOddEven) {
            $xml .= " differentOddEven=\"{$headerFooter->differentOddEven}\"";
        }

        $xml .= '>';

        if (null !== $headerFooter->oddHeader) {
            $xml .= "<oddHeader>{$headerFooter->oddHeader}</oddHeader>";
        }

        if (null !== $headerFooter->oddFooter) {
            $xml .= "<oddFooter>{$headerFooter->oddFooter}</oddFooter>";
        }

        if ($headerFooter->differentOddEven) {
            if (null !== $headerFooter->evenHeader) {
                $xml .= "<evenHeader>{$headerFooter->evenHeader}</evenHeader>";
            }

            if (null !== $headerFooter->evenFooter) {
                $xml .= "<evenFooter>{$headerFooter->evenFooter}</evenFooter>";
            }
        }

        $xml .= '</headerFooter>';

        fwrite($targetResource, $xml);
    }

    /**
     * @param resource $targetResource
     */
    private function getXMLFragmentForPageSetup($targetResource, Options $options): void
    {
        $pageSetup = $options->getPageSetup();
        if (null === $pageSetup) {
            return;
        }

        $xml = '<pageSetup';

        if (null !== $pageSetup->pageOrientation) {
            $xml .= " orientation=\"{$pageSetup->pageOrientation->value}\"";
        }

        if (null !== $pageSetup->paperSize) {
            $xml .= " paperSize=\"{$pageSetup->paperSize->value}\"";
        }

        if (null !== $pageSetup->fitToHeight) {
            $xml .= " fitToHeight=\"{$pageSetup->fitToHeight}\"";
        }

        if (null !== $pageSetup->fitToWidth) {
            $xml .= " fitToWidth=\"{$pageSetup->fitToWidth}\"";
        }

        $xml .= '/>';

        fwrite($targetResource, $xml);
    }

    /**
     * Construct column width references xml to inject into worksheet xml file.
     */
    private function getXMLFragmentForColumnWidths(Options $options, Sheet $sheet): string
    {
        if ([] !== $sheet->getColumnWidths()) {
            $widths = $sheet->getColumnWidths();
        } elseif ([] !== $options->getColumnWidths()) {
            $widths = $options->getColumnWidths();
        } else {
            return '';
        }

        $xml = '<cols>';

        foreach ($widths as $columnWidth) {
            $xml .= '<col min="'.$columnWidth->start.'" max="'.$columnWidth->end.'" width="'.$columnWidth->width.'" customWidth="true"/>';
        }
        $xml .= '</cols>';

        return $xml;
    }

    /**
     * Constructs default row height and width xml to inject into worksheet xml file.
     */
    private function getXMLFragmentForDefaultCellSizing(Options $options): string
    {
        $rowHeightXml = null === $options->DEFAULT_ROW_HEIGHT ? '' : " defaultRowHeight=\"{$options->DEFAULT_ROW_HEIGHT}\"";
        $colWidthXml = null === $options->DEFAULT_COLUMN_WIDTH ? '' : " defaultColWidth=\"{$options->DEFAULT_COLUMN_WIDTH}\"";
        if ('' === $colWidthXml && '' === $rowHeightXml) {
            return '';
        }
        // Ensure that the required defaultRowHeight is set
        $rowHeightXml = '' === $rowHeightXml ? ' defaultRowHeight="0"' : $rowHeightXml;

        return "<sheetFormatPr{$colWidthXml}{$rowHeightXml}/>";
    }

    /**
     * Creates the folder that will be used as root.
     *
     * @throws IOException If unable to create the folder
     */
    private function createRootFolder(): self
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('xlsx', true));

        return $this;
    }

    /**
     * Creates the "_rels" folder under the root folder as well as the ".rels" file in it.
     *
     * @throws IOException If unable to create the folder or the ".rels" file
     */
    private function createRelsFolderAndFile(): self
    {
        $this->relsFolder = $this->createFolder($this->rootFolder, self::RELS_FOLDER_NAME);

        $this->createRelsFile();

        return $this;
    }

    /**
     * Creates the ".rels" file under the "_rels" folder (under root).
     *
     * @throws IOException If unable to create the file
     */
    private function createRelsFile(): self
    {
        $relationshipsXmlContents = <<<'EOD'
            <Relationship Id="rIdWorkbook" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
            <Relationship Id="rIdCore" Type="http://schemas.openxmlformats.org/officedocument/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
            <Relationship Id="rIdApp" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
            EOD;

        if ([] !== $this->properties->customProperties) {
            $relationshipsXmlContents .= <<<'EOD'
                <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties" Target="docProps/custom.xml"/>
                EOD;
        }

        $relsFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">{$relationshipsXmlContents}</Relationships>
            EOD;

        $this->createFileWithContents($this->relsFolder, self::RELS_FILE_NAME, $relsFileContents);

        return $this;
    }

    /**
     * Creates the "docProps" folder under the root folder as well as the "app.xml" and "core.xml" files in it.
     *
     * @throws IOException If unable to create the folder or one of the files
     */
    private function createDocPropsFolderAndFiles(): self
    {
        $this->docPropsFolder = $this->createFolder($this->rootFolder, self::DOC_PROPS_FOLDER_NAME);

        $this->createAppXmlFile();
        $this->createCoreXmlFile();

        if ([] !== $this->properties->customProperties) {
            $this->createCustomXmlFile();
        }

        return $this;
    }

    /**
     * Creates the "app.xml" file under the "docProps" folder.
     *
     * @throws IOException If unable to create the file
     */
    private function createAppXmlFile(): self
    {
        $appXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
                <Application>{$this->properties->application}</Application>
                <TotalTime>0</TotalTime>
            </Properties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::APP_XML_FILE_NAME, $appXmlFileContents);

        return $this;
    }

    /**
     * Creates the "core.xml" file under the "docProps" folder.
     *
     * @throws IOException If unable to create the file
     */
    private function createCoreXmlFile(): self
    {
        $createdDate = (new DateTimeImmutable())->format(DateTimeImmutable::W3C);
        $coreXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <dc:title>{$this->properties->title}</dc:title>
                <dc:subject>{$this->properties->subject}</dc:subject>
                <dc:creator>{$this->properties->creator}</dc:creator>
                <cp:lastModifiedBy>{$this->properties->lastModifiedBy}</cp:lastModifiedBy>
                <cp:keywords>{$this->properties->keywords}</cp:keywords>
                <dc:description>{$this->properties->description}</dc:description>
                <cp:category>{$this->properties->category}</cp:category>
                <dc:language>{$this->properties->language}</dc:language>
                <dcterms:created xsi:type="dcterms:W3CDTF">{$createdDate}</dcterms:created>
                <dcterms:modified xsi:type="dcterms:W3CDTF">{$createdDate}</dcterms:modified>
                <cp:revision>0</cp:revision>
            </cp:coreProperties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::CORE_XML_FILE_NAME, $coreXmlFileContents);

        return $this;
    }

    /**
     * Creates the "custom.xml" file under the "docProps" folder.
     *
     * @throws IOException If unable to create the file
     */
    private function createCustomXmlFile(): self
    {
        /** The pid must increment for each property, starting with 2 */
        $pid = 2;
        $propertiesXmlContents = '';

        foreach ($this->properties->customProperties as $name => $value) {
            $propertiesXmlContents .= <<<EOD
                <property fmtid="{D5CDD505-2E9C-101B-9397-08002B2CF9AE}" pid="{$pid}" name="{$name}"><vt:lpwstr>{$value}</vt:lpwstr></property>
                EOD;

            ++$pid;
        }

        $customXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/custom-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">{$propertiesXmlContents}</Properties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::CUSTOM_XML_FILE_NAME, $customXmlFileContents);

        return $this;
    }

    /**
     * Creates the "xl" folder under the root folder as well as its subfolders.
     *
     * @throws IOException If unable to create at least one of the folders
     */
    private function createXlFolderAndSubFolders(): self
    {
        $this->xlFolder = $this->createFolder($this->rootFolder, self::XL_FOLDER_NAME);
        $this->createXlRelsFolder();
        $this->createXlWorksheetsFolder();
        $this->createDrawingsFolder();

        return $this;
    }

    /**
     * Creates the temp folder where specific sheets content will be written to.
     * This folder is not part of the final ODS file and is only used to be able to jump between sheets.
     *
     * @throws IOException If unable to create the folder
     */
    private function createSheetsContentTempFolder(): self
    {
        $this->sheetsContentTempFolder = $this->createFolder($this->rootFolder, 'worksheets-temp');

        return $this;
    }

    /**
     * Creates the "_rels" folder under the "xl" folder.
     *
     * @throws IOException If unable to create the folder
     */
    private function createXlRelsFolder(): self
    {
        $this->xlRelsFolder = $this->createFolder($this->xlFolder, self::RELS_FOLDER_NAME);

        return $this;
    }

    /**
     * Creates the "drawings" folder under the "xl" folder.
     *
     * @throws IOException If unable to create the folder
     */
    private function createDrawingsFolder(): self
    {
        $this->createFolder($this->getXlFolder(), self::DRAWINGS_FOLDER_NAME);

        return $this;
    }

    /**
     * Creates the "worksheets" folder under the "xl" folder.
     *
     * @throws IOException If unable to create the folder
     */
    private function createXlWorksheetsFolder(): self
    {
        $this->xlWorksheetsFolder = $this->createFolder($this->xlFolder, self::WORKSHEETS_FOLDER_NAME);

        return $this;
    }

    /**
     * Streams the content of the file at the given path into the target resource.
     * Depending on which mode the target resource was created with, it will truncate then copy
     * or append the content to the target file.
     *
     * @param string   $sourceFilePath Path of the file whose content will be copied
     * @param resource $targetResource Target resource that will receive the content
     */
    private function copyFileContentsToTarget(string $sourceFilePath, $targetResource): void
    {
        $sourceHandle = fopen($sourceFilePath, 'r');
        \assert(false !== $sourceHandle);
        stream_copy_to_stream($sourceHandle, $targetResource);
        fclose($sourceHandle);
    }
}
