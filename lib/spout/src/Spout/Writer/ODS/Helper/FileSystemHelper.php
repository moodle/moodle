<?php

namespace Box\Spout\Writer\ODS\Helper;

use Box\Spout\Writer\Common\Entity\Worksheet;
use Box\Spout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use Box\Spout\Writer\Common\Helper\ZipHelper;
use Box\Spout\Writer\ODS\Manager\Style\StyleManager;
use Box\Spout\Writer\ODS\Manager\WorksheetManager;

/**
 * Class FileSystemHelper
 * This class provides helper functions to help with the file system operations
 * like files/folders creation & deletion for ODS files
 */
class FileSystemHelper extends \Box\Spout\Common\Helper\FileSystemHelper implements FileSystemWithRootFolderHelperInterface
{
    const APP_NAME = 'Spout';
    const MIMETYPE = 'application/vnd.oasis.opendocument.spreadsheet';

    const META_INF_FOLDER_NAME = 'META-INF';
    const SHEETS_CONTENT_TEMP_FOLDER_NAME = 'worksheets-temp';

    const MANIFEST_XML_FILE_NAME = 'manifest.xml';
    const CONTENT_XML_FILE_NAME = 'content.xml';
    const META_XML_FILE_NAME = 'meta.xml';
    const MIMETYPE_FILE_NAME = 'mimetype';
    const STYLES_XML_FILE_NAME = 'styles.xml';

    /** @var ZipHelper Helper to perform tasks with Zip archive */
    private $zipHelper;

    /** @var string Path to the root folder inside the temp folder where the files to create the ODS will be stored */
    protected $rootFolder;

    /** @var string Path to the "META-INF" folder inside the root folder */
    protected $metaInfFolder;

    /** @var string Path to the temp folder, inside the root folder, where specific sheets content will be written to */
    protected $sheetsContentTempFolder;

    /**
     * @param string $baseFolderPath The path of the base folder where all the I/O can occur
     * @param ZipHelper $zipHelper Helper to perform tasks with Zip archive
     */
    public function __construct($baseFolderPath, $zipHelper)
    {
        parent::__construct($baseFolderPath);
        $this->zipHelper = $zipHelper;
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
    public function getSheetsContentTempFolder()
    {
        return $this->sheetsContentTempFolder;
    }

    /**
     * Creates all the folders needed to create a ODS file, as well as the files that won't change.
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create at least one of the base folders
     * @return void
     */
    public function createBaseFilesAndFolders()
    {
        $this
            ->createRootFolder()
            ->createMetaInfoFolderAndFile()
            ->createSheetsContentTempFolder()
            ->createMetaFile()
            ->createMimetypeFile();
    }

    /**
     * Creates the folder that will be used as root
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder
     * @return FileSystemHelper
     */
    protected function createRootFolder()
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('ods'));

        return $this;
    }

    /**
     * Creates the "META-INF" folder under the root folder as well as the "manifest.xml" file in it
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder or the "manifest.xml" file
     * @return FileSystemHelper
     */
    protected function createMetaInfoFolderAndFile()
    {
        $this->metaInfFolder = $this->createFolder($this->rootFolder, self::META_INF_FOLDER_NAME);

        $this->createManifestFile();

        return $this;
    }

    /**
     * Creates the "manifest.xml" file under the "META-INF" folder (under root)
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the file
     * @return FileSystemHelper
     */
    protected function createManifestFile()
    {
        $manifestXmlFileContents = <<<'EOD'
<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">
    <manifest:file-entry manifest:full-path="/" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>
    <manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>
    <manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
    <manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>
</manifest:manifest>
EOD;

        $this->createFileWithContents($this->metaInfFolder, self::MANIFEST_XML_FILE_NAME, $manifestXmlFileContents);

        return $this;
    }

    /**
     * Creates the temp folder where specific sheets content will be written to.
     * This folder is not part of the final ODS file and is only used to be able to jump between sheets.
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the folder
     * @return FileSystemHelper
     */
    protected function createSheetsContentTempFolder()
    {
        $this->sheetsContentTempFolder = $this->createFolder($this->rootFolder, self::SHEETS_CONTENT_TEMP_FOLDER_NAME);

        return $this;
    }

    /**
     * Creates the "meta.xml" file under the root folder
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the file
     * @return FileSystemHelper
     */
    protected function createMetaFile()
    {
        $appName = self::APP_NAME;
        $createdDate = (new \DateTime())->format(\DateTime::W3C);

        $metaXmlFileContents = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<office:document-meta office:version="1.2" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
    <office:meta>
        <dc:creator>$appName</dc:creator>
        <meta:creation-date>$createdDate</meta:creation-date>
        <dc:date>$createdDate</dc:date>
    </office:meta>
</office:document-meta>
EOD;

        $this->createFileWithContents($this->rootFolder, self::META_XML_FILE_NAME, $metaXmlFileContents);

        return $this;
    }

    /**
     * Creates the "mimetype" file under the root folder
     *
     * @throws \Box\Spout\Common\Exception\IOException If unable to create the file
     * @return FileSystemHelper
     */
    protected function createMimetypeFile()
    {
        $this->createFileWithContents($this->rootFolder, self::MIMETYPE_FILE_NAME, self::MIMETYPE);

        return $this;
    }

    /**
     * Creates the "content.xml" file under the root folder
     *
     * @param WorksheetManager $worksheetManager
     * @param StyleManager $styleManager
     * @param Worksheet[] $worksheets
     * @return FileSystemHelper
     */
    public function createContentFile($worksheetManager, $styleManager, $worksheets)
    {
        $contentXmlFileContents = <<<'EOD'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<office:document-content office:version="1.2" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:msoxl="http://schemas.microsoft.com/office/excel/formula" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
EOD;

        $contentXmlFileContents .= $styleManager->getContentXmlFontFaceSectionContent();
        $contentXmlFileContents .= $styleManager->getContentXmlAutomaticStylesSectionContent($worksheets);

        $contentXmlFileContents .= '<office:body><office:spreadsheet>';

        $this->createFileWithContents($this->rootFolder, self::CONTENT_XML_FILE_NAME, $contentXmlFileContents);

        // Append sheets content to "content.xml"
        $contentXmlFilePath = $this->rootFolder . '/' . self::CONTENT_XML_FILE_NAME;
        $contentXmlHandle = fopen($contentXmlFilePath, 'a');

        foreach ($worksheets as $worksheet) {
            // write the "<table:table>" node, with the final sheet's name
            fwrite($contentXmlHandle, $worksheetManager->getTableElementStartAsString($worksheet));

            $worksheetFilePath = $worksheet->getFilePath();
            $this->copyFileContentsToTarget($worksheetFilePath, $contentXmlHandle);

            fwrite($contentXmlHandle, '</table:table>');
        }

        $contentXmlFileContents = '</office:spreadsheet></office:body></office:document-content>';

        fwrite($contentXmlHandle, $contentXmlFileContents);
        fclose($contentXmlHandle);

        return $this;
    }

    /**
     * Streams the content of the file at the given path into the target resource.
     * Depending on which mode the target resource was created with, it will truncate then copy
     * or append the content to the target file.
     *
     * @param string $sourceFilePath Path of the file whose content will be copied
     * @param resource $targetResource Target resource that will receive the content
     * @return void
     */
    protected function copyFileContentsToTarget($sourceFilePath, $targetResource)
    {
        $sourceHandle = fopen($sourceFilePath, 'r');
        stream_copy_to_stream($sourceHandle, $targetResource);
        fclose($sourceHandle);
    }

    /**
     * Deletes the temporary folder where sheets content was stored.
     *
     * @return FileSystemHelper
     */
    public function deleteWorksheetTempFolder()
    {
        $this->deleteFolderRecursively($this->sheetsContentTempFolder);

        return $this;
    }

    /**
     * Creates the "styles.xml" file under the root folder
     *
     * @param StyleManager $styleManager
     * @param int $numWorksheets Number of created worksheets
     * @return FileSystemHelper
     */
    public function createStylesFile($styleManager, $numWorksheets)
    {
        $stylesXmlFileContents = $styleManager->getStylesXMLFileContent($numWorksheets);
        $this->createFileWithContents($this->rootFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

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
        $zip = $this->zipHelper->createZip($this->rootFolder);

        $zipFilePath = $this->zipHelper->getZipFilePath($zip);

        // In order to have the file's mime type detected properly, files need to be added
        // to the zip file in a particular order.
        // @see http://www.jejik.com/articles/2010/03/how_to_correctly_create_odf_documents_using_zip/
        $this->zipHelper->addUncompressedFileToArchive($zip, $this->rootFolder, self::MIMETYPE_FILE_NAME);

        $this->zipHelper->addFolderToArchive($zip, $this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $this->zipHelper->closeArchiveAndCopyToStream($zip, $streamPointer);

        // once the zip is copied, remove it
        $this->deleteFile($zipFilePath);
    }
}
