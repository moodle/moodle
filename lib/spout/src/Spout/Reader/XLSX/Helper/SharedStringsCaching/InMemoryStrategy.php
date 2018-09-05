<?php

namespace Box\Spout\Reader\XLSX\Helper\SharedStringsCaching;

use Box\Spout\Reader\Exception\SharedStringNotFoundException;

/**
 * Class InMemoryStrategy
 *
 * This class implements the in-memory caching strategy for shared strings.
 * This strategy is used when the number of unique strings is low, compared to the memory available.
 *
 * @package Box\Spout\Reader\XLSX\Helper\SharedStringsCaching
 */
class InMemoryStrategy implements CachingStrategyInterface
{
    /** @var \SplFixedArray Array used to cache the shared strings */
    protected $inMemoryCache;

    /** @var bool Whether the cache has been closed */
    protected $isCacheClosed;

    /**
     * @param int $sharedStringsUniqueCount Number of unique shared strings
     */
    public function __construct($sharedStringsUniqueCount)
    {
        $this->inMemoryCache = new \SplFixedArray($sharedStringsUniqueCount);
        $this->isCacheClosed = false;
    }

    /**
     * Adds the given string to the cache.
     *
     * @param string $sharedString The string to be added to the cache
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     * @return void
     */
    public function addStringForIndex($sharedString, $sharedStringIndex)
    {
        if (!$this->isCacheClosed) {
            $this->inMemoryCache->offsetSet($sharedStringIndex, $sharedString);
        }
    }

    /**
     * Closes the cache after the last shared string was added.
     * This prevents any additional string from being added to the cache.
     *
     * @return void
     */
    public function closeCache()
    {
        $this->isCacheClosed = true;
    }

    /**
     * Returns the string located at the given index from the cache.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     * @return string The shared string at the given index
     * @throws \Box\Spout\Reader\Exception\SharedStringNotFoundException If no shared string found for the given index
     */
    public function getStringAtIndex($sharedStringIndex)
    {
        try {
            return $this->inMemoryCache->offsetGet($sharedStringIndex);
        } catch (\RuntimeException $e) {
            throw new SharedStringNotFoundException("Shared string not found for index: $sharedStringIndex");
        }
    }

    /**
     * Destroys the cache, freeing memory and removing any created artifacts
     *
     * @return void
     */
    public function clearCache()
    {
        unset($this->inMemoryCache);
        $this->isCacheClosed = false;
    }
}
