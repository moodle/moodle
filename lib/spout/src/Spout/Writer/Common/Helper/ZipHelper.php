<?php

namespace Box\Spout\Writer\Common\Helper;

/**
 * Class ZipHelper
 * This class provides helper functions to create zip files
 *
 * @package Box\Spout\Writer\Common\Helper
 */
class ZipHelper
{
    const ZIP_EXTENSION = '.zip';

    /** Controls what to do when trying to add an existing file */
    const EXISTING_FILES_SKIP = 'skip';
    const EXISTING_FILES_OVERWRITE = 'overwrite';

    /** @var string Path of the folder where the zip file will be created */
    protected $tmpFolderPath;

    /** @var \ZipArchive The ZipArchive instance */
    protected $zip;

    /**
     * @param string $tmpFolderPath Path of the temp folder where the zip file will be created
     */
    public function __construct($tmpFolderPath)
    {
        $this->tmpFolderPath = $tmpFolderPath;
    }

    /**
     * Returns the already created ZipArchive instance or
     * creates one if none exists.
     *
     * @return \ZipArchive
     */
    protected function createOrGetZip()
    {
        if (!isset($this->zip)) {
            $this->zip = new \ZipArchive();
            $zipFilePath = $this->getZipFilePath();

            $this->zip->open($zipFilePath, \ZipArchive::CREATE|\ZipArchive::OVERWRITE);
        }

        return $this->zip;
    }

    /**
     * @return string Path where the zip file of the given folder will be created
     */
    public function getZipFilePath()
    {
        return $this->tmpFolderPath . self::ZIP_EXTENSION;
    }

    /**
     * Adds the given file, located under the given root folder to the archive.
     * The file will be compressed.
     *
     * Example of use:
     *   addFileToArchive('/tmp/xlsx/foo', 'bar/baz.xml');
     *   => will add the file located at '/tmp/xlsx/foo/bar/baz.xml' in the archive, but only as 'bar/baz.xml'
     *
     * @param string $rootFolderPath Path of the root folder that will be ignored in the archive tree.
     * @param string $localFilePath Path of the file to be added, under the root folder
     * @param string|void $existingFileMode Controls what to do when trying to add an existing file
     * @return void
     */
    public function addFileToArchive($rootFolderPath, $localFilePath, $existingFileMode = self::EXISTING_FILES_OVERWRITE)
    {
        $this->addFileToArchiveWithCompressionMethod(
            $rootFolderPath,
            $localFilePath,
            $existingFileMode,
            \ZipArchive::CM_DEFAULT
        );
    }

    /**
     * Adds the given file, located under the given root folder to the archive.
     * The file will NOT be compressed.
     *
     * Example of use:
     *   addUncompressedFileToArchive('/tmp/xlsx/foo', 'bar/baz.xml');
     *   => will add the file located at '/tmp/xlsx/foo/bar/baz.xml' in the archive, but only as 'bar/baz.xml'
     *
     * @param string $rootFolderPath Path of the root folder that will be ignored in the archive tree.
     * @param string $localFilePath Path of the file to be added, under the root folder
     * @param string|void $existingFileMode Controls what to do when trying to add an existing file
     * @return void
     */
    public function addUncompressedFileToArchive($rootFolderPath, $localFilePath, $existingFileMode = self::EXISTING_FILES_OVERWRITE)
    {
        $this->addFileToArchiveWithCompressionMethod(
            $rootFolderPath,
            $localFilePath,
            $existingFileMode,
            \ZipArchive::CM_STORE
        );
    }

    /**
     * Adds the given file, located under the given root folder to the archive.
     * The file will NOT be compressed.
     *
     * Example of use:
     *   addUncompressedFileToArchive('/tmp/xlsx/foo', 'bar/baz.xml');
     *   => will add the file located at '/tmp/xlsx/foo/bar/baz.xml' in the archive, but only as 'bar/baz.xml'
     *
     * @param string $rootFolderPath Path of the root folder that will be ignored in the archive tree.
     * @param string $localFilePath Path of the file to be added, under the root folder
     * @param string $existingFileMode Controls what to do when trying to add an existing file
     * @param int $compressionMethod The compression method
     * @return void
     */
    protected function addFileToArchiveWithCompressionMethod($rootFolderPath, $localFilePath, $existingFileMode, $compressionMethod)
    {
        $zip = $this->createOrGetZip();

        if (!$this->shouldSkipFile($zip, $localFilePath, $existingFileMode)) {
            $normalizedFullFilePath = $this->getNormalizedRealPath($rootFolderPath . '/' . $localFilePath);
            $zip->addFile($normalizedFullFilePath, $localFilePath);

            if (self::canChooseCompressionMethod()) {
                $zip->setCompressionName($localFilePath, $compressionMethod);
            }
        }
    }

    /**
     * @return bool Whether it is possible to choose the desired compression method to be used
     */
    public static function canChooseCompressionMethod()
    {
        // setCompressionName() is a PHP7+ method...
        return (method_exists(new \ZipArchive(), 'setCompressionName'));
    }

    /**
     * @param string $folderPath Path to the folder to be zipped
     * @param string|void $existingFileMode Controls what to do when trying to add an existing file
     * @return void
     */
    public function addFolderToArchive($folderPath, $existingFileMode = self::EXISTING_FILES_OVERWRITE)
    {
        $zip = $this->createOrGetZip();

        $folderRealPath = $this->getNormalizedRealPath($folderPath) . '/';
        $itemIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($itemIterator as $itemInfo) {
            $itemRealPath = $this->getNormalizedRealPath($itemInfo->getPathname());
            $itemLocalPath = str_replace($folderRealPath, '', $itemRealPath);

            if ($itemInfo->isFile() && !$this->shouldSkipFile($zip, $itemLocalPath, $existingFileMode)) {
                $zip->addFile($itemRealPath, $itemLocalPath);
            }
        }
    }

    /**
     * @param \ZipArchive $zip
     * @param string $itemLocalPath
     * @param string $existingFileMode
     * @return bool Whether the file should be added to the archive or skipped
     */
    protected function shouldSkipFile($zip, $itemLocalPath, $existingFileMode)
    {
        // Skip files if:
        //   - EXISTING_FILES_SKIP mode chosen
        //   - File already exists in the archive
        return ($existingFileMode === self::EXISTING_FILES_SKIP && $zip->locateName($itemLocalPath) !== false);
    }

    /**
     * Returns canonicalized absolute pathname, containing only forward slashes.
     *
     * @param string $path Path to normalize
     * @return string Normalized and canonicalized path
     */
    protected function getNormalizedRealPath($path)
    {
        $realPath = realpath($path);
        return str_replace(DIRECTORY_SEPARATOR, '/', $realPath);
    }

    /**
     * Closes the archive and copies it into the given stream
     *
     * @param resource $streamPointer Pointer to the stream to copy the zip
     * @return void
     */
    public function closeArchiveAndCopyToStream($streamPointer)
    {
        $zip = $this->createOrGetZip();
        $zip->close();
        unset($this->zip);

        $this->copyZipToStream($streamPointer);
    }

    /**
     * Streams the contents of the zip file into the given stream
     *
     * @param resource $pointer Pointer to the stream to copy the zip
     * @return void
     */
    protected function copyZipToStream($pointer)
    {
        $zipFilePointer = fopen($this->getZipFilePath(), 'r');
        stream_copy_to_stream($zipFilePointer, $pointer);
        fclose($zipFilePointer);
    }
}
