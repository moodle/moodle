<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Helper;

use DateTimeImmutable;
use OpenSpout\Common\Helper\FileSystemHelper as CommonFileSystemHelper;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\ODS\Manager\Style\StyleManager;
use OpenSpout\Writer\ODS\Manager\WorksheetManager;

/**
 * @internal
 */
final class FileSystemHelper implements FileSystemWithRootFolderHelperInterface
{
    public const MIMETYPE = 'application/vnd.oasis.opendocument.spreadsheet';

    public const META_INF_FOLDER_NAME = 'META-INF';

    public const MANIFEST_XML_FILE_NAME = 'manifest.xml';
    public const CONTENT_XML_FILE_NAME = 'content.xml';
    public const META_XML_FILE_NAME = 'meta.xml';
    public const MIMETYPE_FILE_NAME = 'mimetype';
    public const STYLES_XML_FILE_NAME = 'styles.xml';

    private string $baseFolderRealPath;

    /** @var string document creator */
    private string $creator;
    private CommonFileSystemHelper $baseFileSystemHelper;

    /** @var string Path to the root folder inside the temp folder where the files to create the ODS will be stored */
    private string $rootFolder;

    /** @var string Path to the "META-INF" folder inside the root folder */
    private string $metaInfFolder;

    /** @var string Path to the temp folder, inside the root folder, where specific sheets content will be written to */
    private string $sheetsContentTempFolder;

    /** @var ZipHelper Helper to perform tasks with Zip archive */
    private ZipHelper $zipHelper;

    /**
     * @param string    $baseFolderPath The path of the base folder where all the I/O can occur
     * @param ZipHelper $zipHelper      Helper to perform tasks with Zip archive
     * @param string    $creator        document creator
     */
    public function __construct(string $baseFolderPath, ZipHelper $zipHelper, string $creator)
    {
        $this->baseFileSystemHelper = new CommonFileSystemHelper($baseFolderPath);
        $this->baseFolderRealPath = $this->baseFileSystemHelper->getBaseFolderRealPath();
        $this->zipHelper = $zipHelper;
        $this->creator = $creator;
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

    public function getSheetsContentTempFolder(): string
    {
        return $this->sheetsContentTempFolder;
    }

    /**
     * Creates all the folders needed to create a ODS file, as well as the files that won't change.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create at least one of the base folders
     */
    public function createBaseFilesAndFolders(): void
    {
        $this
            ->createRootFolder()
            ->createMetaInfoFolderAndFile()
            ->createSheetsContentTempFolder()
            ->createMetaFile()
            ->createMimetypeFile()
        ;
    }

    /**
     * Creates the "content.xml" file under the root folder.
     *
     * @param Worksheet[] $worksheets
     */
    public function createContentFile(WorksheetManager $worksheetManager, StyleManager $styleManager, array $worksheets): self
    {
        $contentXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <office:document-content office:version="1.2" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:msoxl="http://schemas.microsoft.com/office/excel/formula" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
            EOD;

        $contentXmlFileContents .= $styleManager->getContentXmlFontFaceSectionContent();
        $contentXmlFileContents .= $styleManager->getContentXmlAutomaticStylesSectionContent($worksheets);

        $contentXmlFileContents .= '<office:body><office:spreadsheet>';

        $topContentTempFile = uniqid(self::CONTENT_XML_FILE_NAME);
        $this->createFileWithContents($this->rootFolder, $topContentTempFile, $contentXmlFileContents);

        // Append sheets content to "content.xml"
        $contentXmlFilePath = $this->rootFolder.\DIRECTORY_SEPARATOR.self::CONTENT_XML_FILE_NAME;
        $contentXmlHandle = fopen($contentXmlFilePath, 'w');
        \assert(false !== $contentXmlHandle);

        $topContentTempPathname = $this->rootFolder.\DIRECTORY_SEPARATOR.$topContentTempFile;
        $topContentTempHandle = fopen($topContentTempPathname, 'r');
        \assert(false !== $topContentTempHandle);
        stream_copy_to_stream($topContentTempHandle, $contentXmlHandle);
        fclose($topContentTempHandle);
        unlink($topContentTempPathname);

        foreach ($worksheets as $worksheet) {
            // write the "<table:table>" node, with the final sheet's name
            fwrite($contentXmlHandle, $worksheetManager->getTableElementStartAsString($worksheet));

            $worksheetFilePath = $worksheet->getFilePath();
            $this->copyFileContentsToTarget($worksheetFilePath, $contentXmlHandle);

            fwrite($contentXmlHandle, '</table:table>');
        }

        // add AutoFilter
        $databaseRanges = '';
        foreach ($worksheets as $worksheet) {
            $databaseRanges .= $worksheetManager->getTableDatabaseRangeElementAsString($worksheet);
        }
        if ('' !== $databaseRanges) {
            fwrite($contentXmlHandle, '<table:database-ranges>');
            fwrite($contentXmlHandle, $databaseRanges);
            fwrite($contentXmlHandle, '</table:database-ranges>');
        }

        $contentXmlFileContents = '</office:spreadsheet></office:body></office:document-content>';

        fwrite($contentXmlHandle, $contentXmlFileContents);
        fclose($contentXmlHandle);

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
     * Creates the "styles.xml" file under the root folder.
     *
     * @param int $numWorksheets Number of created worksheets
     */
    public function createStylesFile(StyleManager $styleManager, int $numWorksheets): self
    {
        $stylesXmlFileContents = $styleManager->getStylesXMLFileContent($numWorksheets);
        $this->createFileWithContents($this->rootFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

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
        // @see http://www.jejik.com/articles/2010/03/how_to_correctly_create_odf_documents_using_zip/
        $this->zipHelper->addUncompressedFileToArchive($zip, $this->rootFolder, self::MIMETYPE_FILE_NAME);

        $this->zipHelper->addFolderToArchive($zip, $this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $this->zipHelper->closeArchiveAndCopyToStream($zip, $streamPointer);

        // once the zip is copied, remove it
        $this->deleteFile($zipFilePath);
    }

    /**
     * Creates the folder that will be used as root.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder
     */
    private function createRootFolder(): self
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('ods'));

        return $this;
    }

    /**
     * Creates the "META-INF" folder under the root folder as well as the "manifest.xml" file in it.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder or the "manifest.xml" file
     */
    private function createMetaInfoFolderAndFile(): self
    {
        $this->metaInfFolder = $this->createFolder($this->rootFolder, self::META_INF_FOLDER_NAME);

        $this->createManifestFile();

        return $this;
    }

    /**
     * Creates the "manifest.xml" file under the "META-INF" folder (under root).
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file
     */
    private function createManifestFile(): self
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
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder
     */
    private function createSheetsContentTempFolder(): self
    {
        $this->sheetsContentTempFolder = $this->createFolder($this->rootFolder, 'worksheets-temp');

        return $this;
    }

    /**
     * Creates the "meta.xml" file under the root folder.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file
     */
    private function createMetaFile(): self
    {
        $createdDate = (new DateTimeImmutable())->format(DateTimeImmutable::W3C);

        $metaXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <office:document-meta office:version="1.2" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
                <office:meta>
                    <dc:creator>{$this->creator}</dc:creator>
                    <meta:creation-date>{$createdDate}</meta:creation-date>
                    <dc:date>{$createdDate}</dc:date>
                </office:meta>
            </office:document-meta>
            EOD;

        $this->createFileWithContents($this->rootFolder, self::META_XML_FILE_NAME, $metaXmlFileContents);

        return $this;
    }

    /**
     * Creates the "mimetype" file under the root folder.
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file
     */
    private function createMimetypeFile(): self
    {
        $this->createFileWithContents($this->rootFolder, self::MIMETYPE_FILE_NAME, self::MIMETYPE);

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
