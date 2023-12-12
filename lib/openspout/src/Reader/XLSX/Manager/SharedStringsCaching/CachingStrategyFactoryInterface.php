<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

interface CachingStrategyFactoryInterface
{
    /**
     * Returns the best caching strategy, given the number of unique shared strings
     * and the amount of memory available.
     *
     * @param null|int $sharedStringsUniqueCount Number of unique shared strings (NULL if unknown)
     * @param string   $tempFolder               Temporary folder where the temporary files to store shared strings will be stored
     *
     * @return CachingStrategyInterface The best caching strategy
     */
    public function createBestCachingStrategy(?int $sharedStringsUniqueCount, string $tempFolder): CachingStrategyInterface;
}
