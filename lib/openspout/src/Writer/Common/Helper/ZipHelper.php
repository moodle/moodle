<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;

/**
 * @internal
 */
final class ZipHelper
{
    public const ZIP_EXTENSION = '.zip';

    /**
     * Controls what to do when trying to add an existing file.
     */
    public const EXISTING_FILES_SKIP = 'skip';
    public const EXISTING_FILES_OVERWRITE = 'overwrite';

    /**
     * Returns a new ZipArchive instance pointing at the given path.
     *
     * @param string $tmpFolderPath Path of the temp folder where the zip file will be created
     */
    public function createZip(string $tmpFolderPath): ZipArchive
    {
        $zip = new ZipArchive();
        $zipFilePath = $tmpFolderPath.self::ZIP_EXTENSION;

        $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        return $zip;
    }

    /**
     * @param ZipArchive $zip An opened zip archive object
     *
     * @return string Path where the zip file of the given folder will be created
     */
    public function getZipFilePath(ZipArchive $zip): string
    {
        return $zip->filename;
    }

    /**
     * Adds the given file, located under the given root folder to the archive.
     * The file will be compressed.
     *
     * Example of use:
     *   addFileToArchive($zip, '/tmp/xlsx/foo', 'bar/baz.xml');
     *   => will add the file located at '/tmp/xlsx/foo/bar/baz.xml' in the archive, but only as 'bar/baz.xml'
     *
     * @param ZipArchive $zip              An opened zip archive object
     * @param string     $rootFolderPath   path of the root folder that will be ignored in the archive tree
     * @param string     $localFilePath    Path of the file to be added, under the root folder
     * @param string     $existingFileMode Controls what to do when trying to add an existing file
     */
    public function addFileToArchive(ZipArchive $zip, string $rootFolderPath, string $localFilePath, string $existingFileMode = self::EXISTING_FILES_OVERWRITE): void
    {
        $this->addFileToArchiveWithCompressionMethod(
            $zip,
            $rootFolderPath,
            $localFilePath,
            $existingFileMode,
            ZipArchive::CM_DEFAULT
        );
    }

    /**
     * Adds the given file, located under the given root folder to the archive.
     * The file will NOT be compressed.
     *
     * Example of use:
     *   addUncompressedFileToArchive($zip, '/tmp/xlsx/foo', 'bar/baz.xml');
     *   => will add the file located at '/tmp/xlsx/foo/bar/baz.xml' in the archive, but only as 'bar/baz.xml'
     *
     * @param ZipArchive $zip              An opened zip archive object
     * @param string     $rootFolderPath   path of the root folder that will be ignored in the archive tree
     * @param string     $localFilePath    Path of the file to be added, under the root folder
     * @param string     $existingFileMode Controls what to do when trying to add an existing file
     */
    public function addUncompressedFileToArchive(ZipArchive $zip, string $rootFolderPath, string $localFilePath, string $existingFileMode = self::EXISTING_FILES_OVERWRITE): void
    {
        $this->addFileToArchiveWithCompressionMethod(
            $zip,
            $rootFolderPath,
            $localFilePath,
            $existingFileMode,
            ZipArchive::CM_STORE
        );
    }

    /**
     * @param ZipArchive $zip              An opened zip archive object
     * @param string     $folderPath       Path to the folder to be zipped
     * @param string     $existingFileMode Controls what to do when trying to add an existing file
     */
    public function addFolderToArchive(ZipArchive $zip, string $folderPath, string $existingFileMode = self::EXISTING_FILES_OVERWRITE): void
    {
        $folderRealPath = $this->getNormalizedRealPath($folderPath).'/';
        $itemIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($itemIterator as $itemInfo) {
            \assert($itemInfo instanceof SplFileInfo);
            $itemRealPath = $this->getNormalizedRealPath($itemInfo->getPathname());
            $itemLocalPath = str_replace($folderRealPath, '', $itemRealPath);

            if ($itemInfo->isFile() && !$this->shouldSkipFile($zip, $itemLocalPath, $existingFileMode)) {
                $zip->addFile($itemRealPath, $itemLocalPath);
            }
        }
    }

    /**
     * Closes the archive and copies it into the given stream.
     *
     * @param ZipArchive $zip           An opened zip archive object
     * @param resource   $streamPointer Pointer to the stream to copy the zip
     */
    public function closeArchiveAndCopyToStream(ZipArchive $zip, $streamPointer): void
    {
        $zipFilePath = $zip->filename;
        $zip->close();

        $this->copyZipToStream($zipFilePath, $streamPointer);
    }

    /**
     * Adds the given file, located under the given root folder to the archive.
     * The file will NOT be compressed.
     *
     * Example of use:
     *   addUncompressedFileToArchive($zip, '/tmp/xlsx/foo', 'bar/baz.xml');
     *   => will add the file located at '/tmp/xlsx/foo/bar/baz.xml' in the archive, but only as 'bar/baz.xml'
     *
     * @param ZipArchive $zip               An opened zip archive object
     * @param string     $rootFolderPath    path of the root folder that will be ignored in the archive tree
     * @param string     $localFilePath     Path of the file to be added, under the root folder
     * @param string     $existingFileMode  Controls what to do when trying to add an existing file
     * @param int        $compressionMethod The compression method
     */
    private function addFileToArchiveWithCompressionMethod(ZipArchive $zip, string $rootFolderPath, string $localFilePath, string $existingFileMode, int $compressionMethod): void
    {
        $normalizedLocalFilePath = str_replace('\\', '/', $localFilePath);
        if (!$this->shouldSkipFile($zip, $normalizedLocalFilePath, $existingFileMode)) {
            $normalizedFullFilePath = $this->getNormalizedRealPath($rootFolderPath.'/'.$normalizedLocalFilePath);
            $zip->addFile($normalizedFullFilePath, $normalizedLocalFilePath);

            $zip->setCompressionName($normalizedLocalFilePath, $compressionMethod);
        }
    }

    /**
     * @return bool Whether the file should be added to the archive or skipped
     */
    private function shouldSkipFile(ZipArchive $zip, string $itemLocalPath, string $existingFileMode): bool
    {
        // Skip files if:
        //   - EXISTING_FILES_SKIP mode chosen
        //   - File already exists in the archive
        return self::EXISTING_FILES_SKIP === $existingFileMode && false !== $zip->locateName($itemLocalPath);
    }

    /**
     * Returns canonicalized absolute pathname, containing only forward slashes.
     *
     * @param string $path Path to normalize
     *
     * @return string Normalized and canonicalized path
     */
    private function getNormalizedRealPath(string $path): string
    {
        $realPath = realpath($path);
        \assert(false !== $realPath);

        return str_replace(\DIRECTORY_SEPARATOR, '/', $realPath);
    }

    /**
     * Streams the contents of the zip file into the given stream.
     *
     * @param string   $zipFilePath Path of the zip file
     * @param resource $pointer     Pointer to the stream to copy the zip
     */
    private function copyZipToStream(string $zipFilePath, $pointer): void
    {
        $zipFilePointer = fopen($zipFilePath, 'r');
        \assert(false !== $zipFilePointer);
        stream_copy_to_stream($zipFilePointer, $pointer);
        fclose($zipFilePointer);
    }
}
