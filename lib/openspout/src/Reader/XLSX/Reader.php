<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Reader\AbstractReader;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactory;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactoryInterface;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\MemoryLimit;
use OpenSpout\Reader\XLSX\Manager\SharedStringsManager;
use OpenSpout\Reader\XLSX\Manager\SheetManager;
use OpenSpout\Reader\XLSX\Manager\WorkbookRelationshipsManager;
use ZipArchive;

/**
 * @extends AbstractReader<SheetIterator>
 */
final class Reader extends AbstractReader
{
    private ZipArchive $zip;

    /** @var SharedStringsManager Manages shared strings */
    private SharedStringsManager $sharedStringsManager;

    /** @var SheetIterator To iterator over the XLSX sheets */
    private SheetIterator $sheetIterator;

    private Options $options;
    private CachingStrategyFactoryInterface $cachingStrategyFactory;

    public function __construct(
        ?Options $options = null,
        ?CachingStrategyFactoryInterface $cachingStrategyFactory = null
    ) {
        $this->options = $options ?? new Options();

        if (null === $cachingStrategyFactory) {
            $memoryLimit = \ini_get('memory_limit');
            $cachingStrategyFactory = new CachingStrategyFactory(new MemoryLimit($memoryLimit));
        }
        $this->cachingStrategyFactory = $cachingStrategyFactory;
    }

    public function getSheetIterator(): SheetIterator
    {
        $this->ensureStreamOpened();

        return $this->sheetIterator;
    }

    /**
     * Returns whether stream wrappers are supported.
     */
    protected function doesSupportStreamWrapper(): bool
    {
        return false;
    }

    /**
     * Opens the file at the given file path to make it ready to be read.
     * It also parses the sharedStrings.xml file to get all the shared strings available in memory
     * and fetches all the available sheets.
     *
     * @param string $filePath Path of the file to be read
     *
     * @throws \OpenSpout\Common\Exception\IOException            If the file at the given path or its content cannot be read
     * @throws \OpenSpout\Reader\Exception\NoSheetsFoundException If there are no sheets in the file
     */
    protected function openReader(string $filePath): void
    {
        $this->zip = new ZipArchive();

        if (true !== $this->zip->open($filePath)) {
            throw new IOException("Could not open {$filePath} for reading.");
        }

        $this->sharedStringsManager = new SharedStringsManager(
            $filePath,
            $this->options,
            new WorkbookRelationshipsManager($filePath),
            $this->cachingStrategyFactory
        );

        if ($this->sharedStringsManager->hasSharedStrings()) {
            // Extracts all the strings from the sheets for easy access in the future
            $this->sharedStringsManager->extractSharedStrings();
        }

        $this->sheetIterator = new SheetIterator(
            new SheetManager(
                $filePath,
                $this->options,
                $this->sharedStringsManager,
                new XLSX()
            )
        );
    }

    /**
     * Closes the reader. To be used after reading the file.
     */
    protected function closeReader(): void
    {
        $this->zip->close();
        $this->sharedStringsManager->cleanup();
    }
}
