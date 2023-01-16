<?php

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

use OpenSpout\Reader\Exception\SharedStringNotFoundException;
use OpenSpout\Reader\XLSX\Creator\HelperFactory;

/**
 * This class implements the file-based caching strategy for shared strings.
 * Shared strings are stored in small files (with a max number of strings per file).
 * This strategy is slower than an in-memory strategy but is used to avoid out of memory crashes.
 */
class FileBasedStrategy implements CachingStrategyInterface
{
    /** Value to use to escape the line feed character ("\n") */
    public const ESCAPED_LINE_FEED_CHARACTER = '_x000A_';

    /** @var \OpenSpout\Common\Helper\GlobalFunctionsHelper Helper to work with global functions */
    protected $globalFunctionsHelper;

    /** @var \OpenSpout\Common\Helper\FileSystemHelper Helper to perform file system operations */
    protected $fileSystemHelper;

    /** @var string Temporary folder where the temporary files will be created */
    protected $tempFolder;

    /**
     * @var int Maximum number of strings that can be stored in one temp file
     *
     * @see CachingStrategyFactory::MAX_NUM_STRINGS_PER_TEMP_FILE
     */
    protected $maxNumStringsPerTempFile;

    /** @var null|resource Pointer to the last temp file a shared string was written to */
    protected $tempFilePointer;

    /**
     * @var string Path of the temporary file whose contents is currently stored in memory
     *
     * @see CachingStrategyFactory::MAX_NUM_STRINGS_PER_TEMP_FILE
     */
    protected $inMemoryTempFilePath;

    /**
     * @var array Contents of the temporary file that was last read
     *
     * @see CachingStrategyFactory::MAX_NUM_STRINGS_PER_TEMP_FILE
     */
    protected $inMemoryTempFileContents;

    /**
     * @param string        $tempFolder               Temporary folder where the temporary files to store shared strings will be stored
     * @param int           $maxNumStringsPerTempFile Maximum number of strings that can be stored in one temp file
     * @param HelperFactory $helperFactory            Factory to create helpers
     */
    public function __construct($tempFolder, $maxNumStringsPerTempFile, $helperFactory)
    {
        $this->fileSystemHelper = $helperFactory->createFileSystemHelper($tempFolder);
        $this->tempFolder = $this->fileSystemHelper->createFolder($tempFolder, uniqid('sharedstrings'));

        $this->maxNumStringsPerTempFile = $maxNumStringsPerTempFile;

        $this->globalFunctionsHelper = $helperFactory->createGlobalFunctionsHelper();
        $this->tempFilePointer = null;
    }

    /**
     * Adds the given string to the cache.
     *
     * @param string $sharedString      The string to be added to the cache
     * @param int    $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     */
    public function addStringForIndex($sharedString, $sharedStringIndex)
    {
        $tempFilePath = $this->getSharedStringTempFilePath($sharedStringIndex);

        if (!$this->globalFunctionsHelper->file_exists($tempFilePath)) {
            if ($this->tempFilePointer) {
                $this->globalFunctionsHelper->fclose($this->tempFilePointer);
            }
            $this->tempFilePointer = $this->globalFunctionsHelper->fopen($tempFilePath, 'w');
        }

        // The shared string retrieval logic expects each cell data to be on one line only
        // Encoding the line feed character allows to preserve this assumption
        $lineFeedEncodedSharedString = $this->escapeLineFeed($sharedString);

        $this->globalFunctionsHelper->fwrite($this->tempFilePointer, $lineFeedEncodedSharedString.PHP_EOL);
    }

    /**
     * Closes the cache after the last shared string was added.
     * This prevents any additional string from being added to the cache.
     */
    public function closeCache()
    {
        // close pointer to the last temp file that was written
        if ($this->tempFilePointer) {
            $this->globalFunctionsHelper->fclose($this->tempFilePointer);
        }
    }

    /**
     * Returns the string located at the given index from the cache.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     *
     * @throws \OpenSpout\Reader\Exception\SharedStringNotFoundException If no shared string found for the given index
     *
     * @return string The shared string at the given index
     */
    public function getStringAtIndex($sharedStringIndex)
    {
        $tempFilePath = $this->getSharedStringTempFilePath($sharedStringIndex);
        $indexInFile = $sharedStringIndex % $this->maxNumStringsPerTempFile;

        if (!$this->globalFunctionsHelper->file_exists($tempFilePath)) {
            throw new SharedStringNotFoundException("Shared string temp file not found: {$tempFilePath} ; for index: {$sharedStringIndex}");
        }

        if ($this->inMemoryTempFilePath !== $tempFilePath) {
            $this->inMemoryTempFileContents = explode(PHP_EOL, $this->globalFunctionsHelper->file_get_contents($tempFilePath));
            $this->inMemoryTempFilePath = $tempFilePath;
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
    public function clearCache()
    {
        if ($this->tempFolder) {
            $this->fileSystemHelper->deleteFolderRecursively($this->tempFolder);
        }
    }

    /**
     * Returns the path for the temp file that should contain the string for the given index.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     *
     * @return string The temp file path for the given index
     */
    protected function getSharedStringTempFilePath($sharedStringIndex)
    {
        $numTempFile = (int) ($sharedStringIndex / $this->maxNumStringsPerTempFile);

        return $this->tempFolder.'/sharedstrings'.$numTempFile;
    }

    /**
     * Escapes the line feed characters (\n).
     *
     * @param string $unescapedString
     *
     * @return string
     */
    private function escapeLineFeed($unescapedString)
    {
        return str_replace("\n", self::ESCAPED_LINE_FEED_CHARACTER, $unescapedString);
    }

    /**
     * Unescapes the line feed characters (\n).
     *
     * @param string $escapedString
     *
     * @return string
     */
    private function unescapeLineFeed($escapedString)
    {
        return str_replace(self::ESCAPED_LINE_FEED_CHARACTER, "\n", $escapedString);
    }
}
