<?php

namespace OpenSpout\Writer\XLSX\Helper;

use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;

/**
 * This class provides helper functions to help with the file system operations
 * like files/folders creation & deletion for XLSX files.
 */
class FileSystemHelper extends \OpenSpout\Common\Helper\FileSystemHelper implements FileSystemWithRootFolderHelperInterface
{
    public const APP_NAME = 'Spout';

    public const RELS_FOLDER_NAME = '_rels';
    public const DOC_PROPS_FOLDER_NAME = 'docProps';
    public const XL_FOLDER_NAME = 'xl';
    public const WORKSHEETS_FOLDER_NAME = 'worksheets';

    public const RELS_FILE_NAME = '.rels';
    public const APP_XML_FILE_NAME = 'app.xml';
    public const CORE_XML_FILE_NAME = 'core.xml';
    public const CONTENT_TYPES_XML_FILE_NAME = '[Content_Types].xml';
    public const WORKBOOK_XML_FILE_NAME = 'workbook.xml';
    public const WORKBOOK_RELS_XML_FILE_NAME = 'workbook.xml.rels';
    public const STYLES_XML_FILE_NAME = 'styles.xml';

    /** @var ZipHelper Helper to perform tasks with Zip archive */
    private $zipHelper;

    /** @var \OpenSpout\Common\Helper\Escaper\XLSX Used to escape XML data */
    private $escaper;

    /** @var string Path to the root folder inside the temp folder where the files to create the XLSX will be stored */
    private $rootFolder;

    /** @var string Path to the "_rels" folder inside the root folder */
    private $relsFolder;

    /** @var string Path to the "docProps" folder inside the root folder */
    private $docPropsFolder;

    /** @var string Path to the "xl" folder inside the root folder */
    private $xlFolder;

    /** @var string Path to the "_rels" folder inside the "xl" folder */
    private $xlRelsFolder;

    /** @var string Path to the "worksheets" folder inside the "xl" folder */
    private $xlWorksheetsFolder;

    /**
     * @param string                                $baseFolderPath The path of the base folder where all the I/O can occur
     * @param ZipHelper                             $zipHelper      Helper to perform tasks with Zip archive
     * @param \OpenSpout\Common\Helper\Escaper\XLSX $escaper        Used to escape XML data
     */
    public function __construct($baseFolderPath, $zipHelper, $escaper)
    {
        parent::__construct($baseFolderPath);
        $this->zipHelper = $zipHelper;
        $this->escaper = $escaper;
    }

    /**
     * @return string
     */
    public function getRootFolder()
    {
        return $this->rootFolder;
    }

    /**
     * @return string
     */
    public function getXlFolder()
    {
        return $this->xlFolder;
    }

    /**
     * @return string
     */
    public function getXlWorksheetsFolder()
    {
        return $this->xlWorksheetsFolder;
    }

    /**
     * Creates all the folders needed to create a XLSX file, as well as the files that won't change.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create at least one of the base folders
     */
    public function createBaseFilesAndFolders()
    {
        $this
            ->createRootFolder()
            ->createRelsFolderAndFile()
            ->createDocPropsFolderAndFiles()
            ->createXlFolderAndSubFolders()
        ;
    }

    /**
     * Creates the "[Content_Types].xml" file under the root folder.
     *
     * @param Worksheet[] $worksheets
     *
     * @return FileSystemHelper
     */
    public function createContentTypesFile($worksheets)
    {
        $contentTypesXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default ContentType="application/xml" Extension="xml"/>
                <Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/>
            EOD;

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $contentTypesXmlFileContents .= '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet'.$worksheet->getId().'.xml"/>';
        }

        $contentTypesXmlFileContents .= <<<'EOD'
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" PartName="/xl/styles.xml"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml" PartName="/xl/sharedStrings.xml"/>
                <Override ContentType="application/vnd.openxmlformats-package.core-properties+xml" PartName="/docProps/core.xml"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" PartName="/docProps/app.xml"/>
            </Types>
            EOD;

        $this->createFileWithContents($this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME, $contentTypesXmlFileContents);

        return $this;
    }

    /**
     * Creates the "workbook.xml" file under the "xl" folder.
     *
     * @param Worksheet[] $worksheets
     *
     * @return FileSystemHelper
     */
    public function createWorkbookFile($worksheets)
    {
        $workbookXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
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
            </workbook>
            EOD;

        $this->createFileWithContents($this->xlFolder, self::WORKBOOK_XML_FILE_NAME, $workbookXmlFileContents);

        return $this;
    }

    /**
     * Creates the "workbook.xml.res" file under the "xl/_res" folder.
     *
     * @param Worksheet[] $worksheets
     *
     * @return FileSystemHelper
     */
    public function createWorkbookRelsFile($worksheets)
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
     * Creates the "styles.xml" file under the "xl" folder.
     *
     * @param StyleManager $styleManager
     *
     * @return FileSystemHelper
     */
    public function createStylesFile($styleManager)
    {
        $stylesXmlFileContents = $styleManager->getStylesXMLFileContent();
        $this->createFileWithContents($this->xlFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

        return $this;
    }

    /**
     * Zips the root folder and streams the contents of the zip into the given stream.
     *
     * @param resource $streamPointer Pointer to the stream to copy the zip
     */
    public function zipRootFolderAndCopyToStream($streamPointer)
    {
        $zip = $this->zipHelper->createZip($this->rootFolder);

        $zipFilePath = $this->zipHelper->getZipFilePath($zip);

        // In order to have the file's mime type detected properly, files need to be added
        // to the zip file in a particular order.
        // "[Content_Types].xml" then at least 2 files located in "xl" folder should be zipped first.
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME);
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::XL_FOLDER_NAME.'/'.self::WORKBOOK_XML_FILE_NAME);
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::XL_FOLDER_NAME.'/'.self::STYLES_XML_FILE_NAME);

        $this->zipHelper->addFolderToArchive($zip, $this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $this->zipHelper->closeArchiveAndCopyToStream($zip, $streamPointer);

        // once the zip is copied, remove it
        $this->deleteFile($zipFilePath);
    }

    /**
     * Creates the folder that will be used as root.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder
     *
     * @return FileSystemHelper
     */
    private function createRootFolder()
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('xlsx', true));

        return $this;
    }

    /**
     * Creates the "_rels" folder under the root folder as well as the ".rels" file in it.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder or the ".rels" file
     *
     * @return FileSystemHelper
     */
    private function createRelsFolderAndFile()
    {
        $this->relsFolder = $this->createFolder($this->rootFolder, self::RELS_FOLDER_NAME);

        $this->createRelsFile();

        return $this;
    }

    /**
     * Creates the ".rels" file under the "_rels" folder (under root).
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file
     *
     * @return FileSystemHelper
     */
    private function createRelsFile()
    {
        $relsFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rIdWorkbook" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
                <Relationship Id="rIdCore" Type="http://schemas.openxmlformats.org/officedocument/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
                <Relationship Id="rIdApp" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
            </Relationships>
            EOD;

        $this->createFileWithContents($this->relsFolder, self::RELS_FILE_NAME, $relsFileContents);

        return $this;
    }

    /**
     * Creates the "docProps" folder under the root folder as well as the "app.xml" and "core.xml" files in it.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder or one of the files
     *
     * @return FileSystemHelper
     */
    private function createDocPropsFolderAndFiles()
    {
        $this->docPropsFolder = $this->createFolder($this->rootFolder, self::DOC_PROPS_FOLDER_NAME);

        $this->createAppXmlFile();
        $this->createCoreXmlFile();

        return $this;
    }

    /**
     * Creates the "app.xml" file under the "docProps" folder.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file
     *
     * @return FileSystemHelper
     */
    private function createAppXmlFile()
    {
        $appName = self::APP_NAME;
        $appXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
                <Application>{$appName}</Application>
                <TotalTime>0</TotalTime>
            </Properties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::APP_XML_FILE_NAME, $appXmlFileContents);

        return $this;
    }

    /**
     * Creates the "core.xml" file under the "docProps" folder.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file
     *
     * @return FileSystemHelper
     */
    private function createCoreXmlFile()
    {
        $createdDate = (new \DateTime())->format(\DateTime::W3C);
        $coreXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <dcterms:created xsi:type="dcterms:W3CDTF">{$createdDate}</dcterms:created>
                <dcterms:modified xsi:type="dcterms:W3CDTF">{$createdDate}</dcterms:modified>
                <cp:revision>0</cp:revision>
            </cp:coreProperties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::CORE_XML_FILE_NAME, $coreXmlFileContents);

        return $this;
    }

    /**
     * Creates the "xl" folder under the root folder as well as its subfolders.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create at least one of the folders
     *
     * @return FileSystemHelper
     */
    private function createXlFolderAndSubFolders()
    {
        $this->xlFolder = $this->createFolder($this->rootFolder, self::XL_FOLDER_NAME);
        $this->createXlRelsFolder();
        $this->createXlWorksheetsFolder();

        return $this;
    }

    /**
     * Creates the "_rels" folder under the "xl" folder.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder
     *
     * @return FileSystemHelper
     */
    private function createXlRelsFolder()
    {
        $this->xlRelsFolder = $this->createFolder($this->xlFolder, self::RELS_FOLDER_NAME);

        return $this;
    }

    /**
     * Creates the "worksheets" folder under the "xl" folder.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder
     *
     * @return FileSystemHelper
     */
    private function createXlWorksheetsFolder()
    {
        $this->xlWorksheetsFolder = $this->createFolder($this->xlFolder, self::WORKSHEETS_FOLDER_NAME);

        return $this;
    }
}
