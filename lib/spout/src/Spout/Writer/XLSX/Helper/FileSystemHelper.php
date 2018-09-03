<?php

namespace Box\Spout\Writer\XLSX\Helper;

use Box\Spout\Writer\Common\Helper\ZipHelper;
use Box\Spout\Writer\XLSX\Internal\Worksheet;

/**
 * Class FileSystemHelper
 * This class provides helper functions to help with the file system operations
 * like files/folders creation & deletion for XLSX files
 *
 * @package Box\Spout\Writer\XLSX\Helper
 */
class FileSystemHelper extends \Box\Spout\Common\Helper\FileSystemHelper
{
    const APP_NAME = 'Spout';

    const RELS_FOLDER_NAME = '_rels';
    const DOC_PROPS_FOLDER_NAME = 'docProps';
    const XL_FOLDER_NAME = 'xl';
    const WORKSHEETS_FOLDER_NAME = 'worksheets';

    const RELS_FILE_NAME = '.rels';
    const APP_XML_FILE_NAME = 'app.xml';
    const CORE_XML_FILE_NAME = 'core.xml';
    const CONTENT_TYPES_XML_FILE_NAME = '[Content_Types].xml';
    const WORKBOOK_XML_FILE_NAME = 'workbook.xml';
    const WORKBOOK_RELS_XML_FILE_NAME = 'workbook.xml.rels';
    const STYLES_XML_FILE_NAME = 'styles.xml';

    /** @var string Path to the root folder inside the temp folder where the files to create the XLSX will be stored */
    protected $rootFolder;

    /** @var string Path to the "_rels" folder inside the root folder */
    protected $relsFolder;

    /** @var string Path to the "docProps" folder inside the root folder */
    protected $docPropsFolder;

    /** @var string Path to the "xl" folder inside the root folder */
    protected $xlFolder;

    /** @var string Path to the "_rels" folder inside the "xl" folder */
    protected $xlRelsFolder;

    /** @var string Path to the "worksheets" folder inside the "xl" folder */
    protected $xlWorksheetsFolder;

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
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If unable to create at least one of the base folders
     */
    public function createBaseFilesAndFolders()
    {
        $this
            ->createRootFolder()
            ->createRelsFolderAndFile()
            ->createDocPropsFolderAndFiles()
            ->createXlFolderAndSubFolders();
    }

    /**
     * Creates the folder that will be used as root
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder
     */
    protected function createRootFolder()
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('xlsx', true));
        return $this;
    }

    /**
     * Creates the "_rels" folder under the root folder as well as the ".rels" file in it
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder or the ".rels" file
     */
    protected function createRelsFolderAndFile()
    {
        $this->relsFolder = $this->createFolder($this->rootFolder, self::RELS_FOLDER_NAME);

        $this->createRelsFile();

        return $this;
    }

    /**
     * Creates the ".rels" file under the "_rels" folder (under root)
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the file
     */
    protected function createRelsFile()
    {
        $relsFileContents = <<<EOD
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
     * Creates the "docProps" folder under the root folder as well as the "app.xml" and "core.xml" files in it
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder or one of the files
     */
    protected function createDocPropsFolderAndFiles()
    {
        $this->docPropsFolder = $this->createFolder($this->rootFolder, self::DOC_PROPS_FOLDER_NAME);

        $this->createAppXmlFile();
        $this->createCoreXmlFile();

        return $this;
    }

    /**
     * Creates the "app.xml" file under the "docProps" folder
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the file
     */
    protected function createAppXmlFile()
    {
        $appName = self::APP_NAME;
        $appXmlFileContents = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
    <Application>$appName</Application>
    <TotalTime>0</TotalTime>
</Properties>
EOD;

        $this->createFileWithContents($this->docPropsFolder, self::APP_XML_FILE_NAME, $appXmlFileContents);

        return $this;
    }

    /**
     * Creates the "core.xml" file under the "docProps" folder
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the file
     */
    protected function createCoreXmlFile()
    {
        $createdDate = (new \DateTime())->format(\DateTime::W3C);
        $coreXmlFileContents = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dcterms:created xsi:type="dcterms:W3CDTF">$createdDate</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">$createdDate</dcterms:modified>
    <cp:revision>0</cp:revision>
</cp:coreProperties>
EOD;

        $this->createFileWithContents($this->docPropsFolder, self::CORE_XML_FILE_NAME, $coreXmlFileContents);

        return $this;
    }

    /**
     * Creates the "xl" folder under the root folder as well as its subfolders
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create at least one of the folders
     */
    protected function createXlFolderAndSubFolders()
    {
        $this->xlFolder = $this->createFolder($this->rootFolder, self::XL_FOLDER_NAME);
        $this->createXlRelsFolder();
        $this->createXlWorksheetsFolder();

        return $this;
    }

    /**
     * Creates the "_rels" folder under the "xl" folder
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder
     */
    protected function createXlRelsFolder()
    {
        $this->xlRelsFolder = $this->createFolder($this->xlFolder, self::RELS_FOLDER_NAME);
        return $this;
    }

    /**
     * Creates the "worksheets" folder under the "xl" folder
     *
     * @return FileSystemHelper
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder
     */
    protected function createXlWorksheetsFolder()
    {
        $this->xlWorksheetsFolder = $this->createFolder($this->xlFolder, self::WORKSHEETS_FOLDER_NAME);
        return $this;
    }

    /**
     * Creates the "[Content_Types].xml" file under the root folder
     *
     * @param Worksheet[] $worksheets
     * @return FileSystemHelper
     */
    public function createContentTypesFile($worksheets)
    {
        $contentTypesXmlFileContents = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default ContentType="application/xml" Extension="xml"/>
    <Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/>
    <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/>
EOD;

    /** @var Worksheet $worksheet */
    foreach ($worksheets as $worksheet) {
        $contentTypesXmlFileContents .= '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet' . $worksheet->getId() . '.xml"/>';
    }

    $contentTypesXmlFileContents .= <<<EOD
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
     * Creates the "workbook.xml" file under the "xl" folder
     *
     * @param Worksheet[] $worksheets
     * @return FileSystemHelper
     */
    public function createWorkbookFile($worksheets)
    {
        $workbookXmlFileContents = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
EOD;

        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $escaper = \Box\Spout\Common\Escaper\XLSX::getInstance();

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $worksheetName = $worksheet->getExternalSheet()->getName();
            $worksheetId = $worksheet->getId();
            $workbookXmlFileContents .= '<sheet name="' . $escaper->escape($worksheetName) . '" sheetId="' . $worksheetId . '" r:id="rIdSheet' . $worksheetId . '"/>';
        }

        $workbookXmlFileContents .= <<<EOD
    </sheets>
</workbook>
EOD;

        $this->createFileWithContents($this->xlFolder, self::WORKBOOK_XML_FILE_NAME, $workbookXmlFileContents);

        return $this;
    }

    /**
     * Creates the "workbook.xml.res" file under the "xl/_res" folder
     *
     * @param Worksheet[] $worksheets
     * @return FileSystemHelper
     */
    public function createWorkbookRelsFile($worksheets)
    {
        $workbookRelsXmlFileContents = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rIdStyles" Target="styles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/>
    <Relationship Id="rIdSharedStrings" Target="sharedStrings.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"/>
EOD;

        /** @var Worksheet $worksheet */
        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $workbookRelsXmlFileContents .= '<Relationship Id="rIdSheet' . $worksheetId . '" Target="worksheets/sheet' . $worksheetId . '.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>';
        }

        $workbookRelsXmlFileContents .= '</Relationships>';

        $this->createFileWithContents($this->xlRelsFolder, self::WORKBOOK_RELS_XML_FILE_NAME, $workbookRelsXmlFileContents);

        return $this;
    }

    /**
     * Creates the "styles.xml" file under the "xl" folder
     *
     * @param StyleHelper $styleHelper
     * @return FileSystemHelper
     */
    public function createStylesFile($styleHelper)
    {
        $stylesXmlFileContents = $styleHelper->getStylesXMLFileContent();
        $this->createFileWithContents($this->xlFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

        return $this;
    }

    /**
     * Zips the root folder and streams the contents of the zip into the given stream
     *
     * @param resource $streamPointer Pointer to the stream to copy the zip
     * @return void
     */
    public function zipRootFolderAndCopyToStream($streamPointer)
    {
        $zipHelper = new ZipHelper($this->rootFolder);

        // In order to have the file's mime type detected properly, files need to be added
        // to the zip file in a particular order.
        // "[Content_Types].xml" then at least 2 files located in "xl" folder should be zipped first.
        $zipHelper->addFileToArchive($this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME);
        $zipHelper->addFileToArchive($this->rootFolder, self::XL_FOLDER_NAME . '/' . self::WORKBOOK_XML_FILE_NAME);
        $zipHelper->addFileToArchive($this->rootFolder, self::XL_FOLDER_NAME . '/' . self::STYLES_XML_FILE_NAME);

        $zipHelper->addFolderToArchive($this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $zipHelper->closeArchiveAndCopyToStream($streamPointer);

        // once the zip is copied, remove it
        $this->deleteFile($zipHelper->getZipFilePath());
    }
}
