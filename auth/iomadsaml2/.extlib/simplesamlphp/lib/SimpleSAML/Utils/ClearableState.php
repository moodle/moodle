<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

/**
 * Indicates an implementation caches state internally and may be cleared.
 *
 * Primarily designed to allow SSP state to be cleared between unit tests.
 * @package SimpleSAML\Utils
 */
interface ClearableState
{
    /**
     * Clear any cached internal state.
     */
    public static function clearInternalState();
}
