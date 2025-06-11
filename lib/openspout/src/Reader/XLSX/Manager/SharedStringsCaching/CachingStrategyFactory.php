<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

/**
 * @internal
 */
final class CachingStrategyFactory implements CachingStrategyFactoryInterface
{
    /**
     * The memory amount needed to store a string was obtained empirically from this data:.
     *
     *        ------------------------------------
     *        | Number of chars⁺ | Memory needed |
     *        ------------------------------------
     *        |           3,000  |         1 MB  |
     *        |          15,000  |         2 MB  |
     *        |          30,000  |         5 MB  |
     *        |          75,000  |        11 MB  |
     *        |         150,000  |        21 MB  |
     *        |         300,000  |        43 MB  |
     *        |         750,000  |       105 MB  |
     *        |       1,500,000  |       210 MB  |
     *        |       2,250,000  |       315 MB  |
     *        |       3,000,000  |       420 MB  |
     *        |       4,500,000  |       630 MB  |
     *        ------------------------------------
     *
     *        ⁺ All characters were 1 byte long
     *
     * This gives a linear graph where each 1-byte character requires about 150 bytes to be stored.
     * Given that some characters can take up to 4 bytes, we need 600 bytes per character to be safe.
     * Also, there is on average about 20 characters per cell (this is entirely empirical data...).
     *
     * This means that in order to store one shared string in memory, the memory amount needed is:
     *   => 20 * 600 ≈ 12KB
     */
    public const AMOUNT_MEMORY_NEEDED_PER_STRING_IN_KB = 12;

    /**
     * To avoid running out of memory when extracting a huge number of shared strings, they can be saved to temporary files
     * instead of in memory. Then, when accessing a string, the corresponding file contents will be loaded in memory
     * and the string will be quickly retrieved.
     * The performance bottleneck is not when creating these temporary files, but rather when loading their content.
     * Because the contents of the last loaded file stays in memory until another file needs to be loaded, it works
     * best when the indexes of the shared strings are sorted in the sheet data.
     * 10,000 was chosen because it creates small files that are fast to be loaded in memory.
     */
    public const MAX_NUM_STRINGS_PER_TEMP_FILE = 10000;

    private readonly MemoryLimit $memoryLimit;

    public function __construct(MemoryLimit $memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
    }

    /**
     * Returns the best caching strategy, given the number of unique shared strings
     * and the amount of memory available.
     *
     * @param null|int $sharedStringsUniqueCount Number of unique shared strings (NULL if unknown)
     * @param string   $tempFolder               Temporary folder where the temporary files to store shared strings will be stored
     *
     * @return CachingStrategyInterface The best caching strategy
     */
    public function createBestCachingStrategy(?int $sharedStringsUniqueCount, string $tempFolder): CachingStrategyInterface
    {
        if ($this->isInMemoryStrategyUsageSafe($sharedStringsUniqueCount)) {
            return new InMemoryStrategy($sharedStringsUniqueCount);
        }

        return new FileBasedStrategy($tempFolder, self::MAX_NUM_STRINGS_PER_TEMP_FILE);
    }

    /**
     * Returns whether it is safe to use in-memory caching, given the number of unique shared strings
     * and the amount of memory available.
     *
     * @param null|int $sharedStringsUniqueCount Number of unique shared strings (NULL if unknown)
     */
    private function isInMemoryStrategyUsageSafe(?int $sharedStringsUniqueCount): bool
    {
        // if the number of shared strings in unknown, do not use "in memory" strategy
        if (null === $sharedStringsUniqueCount) {
            return false;
        }

        $memoryAvailable = $this->memoryLimit->getMemoryLimitInKB();

        if (-1 === (int) $memoryAvailable) {
            // if cannot get memory limit or if memory limit set as unlimited, don't trust and play safe
            $isInMemoryStrategyUsageSafe = ($sharedStringsUniqueCount < self::MAX_NUM_STRINGS_PER_TEMP_FILE);
        } else {
            $memoryNeeded = $sharedStringsUniqueCount * self::AMOUNT_MEMORY_NEEDED_PER_STRING_IN_KB;
            $isInMemoryStrategyUsageSafe = ($memoryAvailable > $memoryNeeded);
        }

        return $isInMemoryStrategyUsageSafe;
    }
}
