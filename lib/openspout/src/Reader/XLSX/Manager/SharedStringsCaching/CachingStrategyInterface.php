<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

use OpenSpout\Reader\Exception\SharedStringNotFoundException;

/**
 * @internal
 */
interface CachingStrategyInterface
{
    /**
     * Adds the given string to the cache.
     *
     * @param string $sharedString      The string to be added to the cache
     * @param int    $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     */
    public function addStringForIndex(string $sharedString, int $sharedStringIndex): void;

    /**
     * Closes the cache after the last shared string was added.
     * This prevents any additional string from being added to the cache.
     */
    public function closeCache(): void;

    /**
     * Returns the string located at the given index from the cache.
     *
     * @param int $sharedStringIndex Index of the shared string in the sharedStrings.xml file
     *
     * @return string The shared string at the given index
     *
     * @throws SharedStringNotFoundException If no shared string found for the given index
     */
    public function getStringAtIndex(int $sharedStringIndex): string;

    /**
     * Destroys the cache, freeing memory and removing any created artifacts.
     */
    public function clearCache(): void;
}
