<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

use OpenSpout\Common\Helper\FileSystemHelper;
use OpenSpout\Reader\Exception\SharedStringNotFoundException;

/**
 * This class implements the file-based caching strategy for shared strings.
 * Shared strings are stored in small files (with a max number of strings per file).
 * This strategy is slower than an in-memory strategy but is used to avoid out of memory crashes.
 *
 * @internal
 */
final class FileBasedStrategy implements CachingStrategyInterface
{
    /**
     * Value to use to escape the line feed character ("\n").
     */
    public const ESCAPED_LINE_FEED_CHARACTER = '_x000A_';

    /** @var FileSystemHelper Helper to perform file system operations */
    private readonly FileSystemHelper $fileSystemHelper;

    /** @var string Temporary folder where the temporary files will be created */
    private readonly string $tempFolder;

    /**
     * @var int Maximum number of strings that can be stored in one temp file
     *
     * @see CachingStrategyFactory::MAX_NUM_STRINGS_PER_TEMP_FILE
     */
    private readonly int $maxNumStringsPerTempFile;

    /** @var null|resource Pointer to the last temp file a shared string was written to */
    private $tempFilePointer;

    /**
     * @var string Path of the temporary file whose contents is currently stored in memory
     *
     * @see CachingStrategyFactory::MAX_NUM_STRINGS_PER_TEMP_FILE
     */
    private string $readMemoryTempFilePath = '';

    /** @var string Path of the temporary file whose contents is currently being written to */
    private string $writeMemoryTempFilePath = '';

    /**
     * @see CachingStrategyFactory::MAX_NUM_STRINGS_PER_TEMP_FILE
     *
     * @var string[] Contents of the temporary file that was last read
     */
    private array $inMemoryTempFileContents;

    /**
     * @param string $tempFolder               Temporary folder where the temporary files to store shared strings will be stored
     * @param int    $maxNumStringsPerTempFile Maximum number of strings that can be stored in one temp file
     */
    public function __construct(string $tempFolder, int $maxNumStringsPerTempFile)
    {
        $this->fileSystemHelper = new FileSystemHelper($tempFolder);
        $this->tempFolder = $this->fileSystemHelper->createFolder($tempFolder, uniqid('sharedstrings'));

        $this->maxNumStringsPerTempFile = $maxNumStringsPerTempFile;
    }

    /**
     * Adds the given string to the cache.
     *
     * @param string $sharedString      The string to be added to the cache
     * @param int    $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     */
    public function addStringForIndex(string $sharedString, int $sharedStringIndex): void
    {
        $tempFilePath = $this->getSharedStringTempFilePath($sharedStringIndex);

        if ($this->writeMemoryTempFilePath !== $tempFilePath) {
            if (null !== $this->tempFilePointer) {
                fclose($this->tempFilePointer);
            }
            $resource = fopen($tempFilePath, 'w');
            \assert(false !== $resource);
            $this->tempFilePointer = $resource;
            $this->writeMemoryTempFilePath = $tempFilePath;
        }

        // The shared string retrieval logic expects each cell data to be on one line only
        // Encoding the line feed character allows to preserve this assumption
        $lineFeedEncodedSharedString = $this->escapeLineFeed($sharedString);

        fwrite($this->tempFilePointer, $lineFeedEncodedSharedString.PHP_EOL);
    }

    /**
     * Closes the cache after the last shared string was added.
     * This prevents any additional string from being added to the cache.
     */
    public function closeCache(): void
    {
        // close pointer to the last temp file that was written
        if (null !== $this->tempFilePointer) {
            $this->writeMemoryTempFilePath = '';
            fclose($this->tempFilePointer);
        }
    }

    /**
     * Returns the string located at the given index from the cache.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     *
     * @return string The shared string at the given index
     *
     * @throws SharedStringNotFoundException If no shared string found for the given index
     */
    public function getStringAtIndex(int $sharedStringIndex): string
    {
        $tempFilePath = $this->getSharedStringTempFilePath($sharedStringIndex);
        $indexInFile = $sharedStringIndex % $this->maxNumStringsPerTempFile;

        if ($this->readMemoryTempFilePath !== $tempFilePath) {
            $contents = @file_get_contents($tempFilePath);
            if (false === $contents) {
                throw new SharedStringNotFoundException("Shared string temp file could not be read: {$tempFilePath} ; for index: {$sharedStringIndex}");
            }
            $this->inMemoryTempFileContents = explode(PHP_EOL, $contents);
            $this->readMemoryTempFilePath = $tempFilePath;
        }

        $sharedString = null;

        // Using isset here because it is way faster than array_key_exists...
        if (isset($this->inMemoryTempFileContents[$indexInFile])) {
            $escapedSharedString = $this->inMemoryTempFileContents[$indexInFile];
            $sharedString = $this->unescapeLineFeed($escapedSharedString);
        }

        if (null === $sharedString) {
            throw new SharedStringNotFoundException("Shared string not found for index: {$sharedStringIndex}");
        }

        return rtrim($sharedString, PHP_EOL);
    }

    /**
     * Destroys the cache, freeing memory and removing any created artifacts.
     */
    public function clearCache(): void
    {
        $this->fileSystemHelper->deleteFolderRecursively($this->tempFolder);
    }

    /**
     * Returns the path for the temp file that should contain the string for the given index.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     *
     * @return string The temp file path for the given index
     */
    private function getSharedStringTempFilePath(int $sharedStringIndex): string
    {
        $numTempFile = (int) ($sharedStringIndex / $this->maxNumStringsPerTempFile);

        return $this->tempFolder.\DIRECTORY_SEPARATOR.'sharedstrings'.$numTempFile;
    }

    /**
     * Escapes the line feed characters (\n).
     */
    private function escapeLineFeed(string $unescapedString): string
    {
        return str_replace("\n", self::ESCAPED_LINE_FEED_CHARACTER, $unescapedString);
    }

    /**
     * Unescapes the line feed characters (\n).
     */
    private function unescapeLineFeed(string $escapedString): string
    {
        return str_replace(self::ESCAPED_LINE_FEED_CHARACTER, "\n", $escapedString);
    }
}
