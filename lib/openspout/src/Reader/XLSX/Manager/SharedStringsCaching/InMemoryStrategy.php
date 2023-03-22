<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

use OpenSpout\Reader\Exception\SharedStringNotFoundException;
use RuntimeException;
use SplFixedArray;

/**
 * This class implements the in-memory caching strategy for shared strings.
 * This strategy is used when the number of unique strings is low, compared to the memory available.
 *
 * @internal
 */
final class InMemoryStrategy implements CachingStrategyInterface
{
    /** @var SplFixedArray<string> Array used to cache the shared strings */
    private SplFixedArray $inMemoryCache;

    /** @var bool Whether the cache has been closed */
    private bool $isCacheClosed;

    /**
     * @param int $sharedStringsUniqueCount Number of unique shared strings
     */
    public function __construct(int $sharedStringsUniqueCount)
    {
        $this->inMemoryCache = new SplFixedArray($sharedStringsUniqueCount);
        $this->isCacheClosed = false;
    }

    /**
     * Adds the given string to the cache.
     *
     * @param string $sharedString      The string to be added to the cache
     * @param int    $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     */
    public function addStringForIndex(string $sharedString, int $sharedStringIndex): void
    {
        if (!$this->isCacheClosed) {
            $this->inMemoryCache->offsetSet($sharedStringIndex, $sharedString);
        }
    }

    /**
     * Closes the cache after the last shared string was added.
     * This prevents any additional string from being added to the cache.
     */
    public function closeCache(): void
    {
        $this->isCacheClosed = true;
    }

    /**
     * Returns the string located at the given index from the cache.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     *
     * @return string The shared string at the given index
     *
     * @throws \OpenSpout\Reader\Exception\SharedStringNotFoundException If no shared string found for the given index
     */
    public function getStringAtIndex(int $sharedStringIndex): string
    {
        try {
            return $this->inMemoryCache->offsetGet($sharedStringIndex);
        } catch (RuntimeException $e) {
            throw new SharedStringNotFoundException("Shared string not found for index: {$sharedStringIndex}");
        }
    }

    /**
     * Destroys the cache, freeing memory and removing any created artifacts.
     */
    public function clearCache(): void
    {
        $this->inMemoryCache = new SplFixedArray(0);
        $this->isCacheClosed = false;
    }
}
