<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;

/**
 * @internal
 */
final readonly class StringHelper
{
    /** @var bool Whether the mbstring extension is loaded */
    private bool $hasMbstringSupport;

    public function __construct(bool $hasMbstringSupport)
    {
        $this->hasMbstringSupport = $hasMbstringSupport;
    }

    public static function factory(): self
    {
        return new self(\function_exists('mb_strlen'));
    }

    /**
     * Returns the length of the given string.
     * It uses the multi-bytes function is available.
     *
     * @see strlen
     * @see mb_strlen
     */
    public function getStringLength(string $string): int
    {
        return $this->hasMbstringSupport
            ? mb_strlen($string)
            : \strlen($string); // @codeCoverageIgnore
    }

    /**
     * Returns the position of the first occurrence of the given character/substring within the given string.
     * It uses the multi-bytes function is available.
     *
     * @see strpos
     * @see mb_strpos
     *
     * @param string $char   Needle
     * @param string $string Haystack
     *
     * @return int Char/substring's first occurrence position within the string if found (starts at 0) or -1 if not found
     */
    public function getCharFirstOccurrencePosition(string $char, string $string): int
    {
        $position = $this->hasMbstringSupport
            ? mb_strpos($string, $char)
            : strpos($string, $char); // @codeCoverageIgnore

        return (false !== $position) ? $position : -1;
    }

    /**
     * Returns the position of the last occurrence of the given character/substring within the given string.
     * It uses the multi-bytes function is available.
     *
     * @see strrpos
     * @see mb_strrpos
     *
     * @param string $char   Needle
     * @param string $string Haystack
     *
     * @return int Char/substring's last occurrence position within the string if found (starts at 0) or -1 if not found
     */
    public function getCharLastOccurrencePosition(string $char, string $string): int
    {
        $position = $this->hasMbstringSupport
            ? mb_strrpos($string, $char)
            : strrpos($string, $char); // @codeCoverageIgnore

        return (false !== $position) ? $position : -1;
    }
}
