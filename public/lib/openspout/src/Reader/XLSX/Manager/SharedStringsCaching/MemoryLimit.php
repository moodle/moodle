<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

/**
 * @internal
 */
final readonly class MemoryLimit
{
    private string $memoryLimit;

    public function __construct(string $memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
    }

    /**
     * Returns the PHP "memory_limit" in Kilobytes.
     */
    public function getMemoryLimitInKB(): float
    {
        $memoryLimitFormatted = strtolower(trim($this->memoryLimit));

        // No memory limit
        if ('-1' === $memoryLimitFormatted) {
            return -1;
        }

        if (1 === preg_match('/(\d+)([bkmgt])b?/', $memoryLimitFormatted, $matches)) {
            $amount = (int) $matches[1];
            $unit = $matches[2];

            switch ($unit) {
                case 'b': return $amount / 1024;

                case 'k': return $amount;

                case 'm': return $amount * 1024;

                case 'g': return $amount * 1024 * 1024;

                case 't': return $amount * 1024 * 1024 * 1024;
            }
        }

        return -1;
    }
}
