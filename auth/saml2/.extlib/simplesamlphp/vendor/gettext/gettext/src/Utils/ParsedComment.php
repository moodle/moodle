<?php

namespace Gettext\Utils;

/**
 * Comment parsed by PhpFunctionsScanner.
 */
class ParsedComment
{
    /**
     * The comment itself.
     *
     * @var string
     */
    protected $comment;

    /**
     * The line where the comment starts.
     *
     * @var int
     */
    protected $firstLine;

    /**
     * The line where the comment ends.
     *
     * @var int
     */
    protected $lastLine;

    /**
     * Initializes the instance.
     *
     * @param string $comment The comment itself.
     * @param int    $firstLine The line where the comment starts.
     * @param int    $lastLine The line where the comment ends.
     */
    public function __construct($comment, $firstLine, $lastLine)
    {
        $this->comment = $comment;
        $this->firstLine = $firstLine;
        $this->lastLine = $lastLine;
    }

    /**
     * Create new object from raw comment data.
     *
     * @param string $value The PHP comment string.
     * @param int $line The line where the comment starts.
     *
     * @return static The parsed comment.
     */
    public static function create($value, $line)
    {
        $lastLine = $line + substr_count($value, "\n");

        $lines = array_map(function ($line) {
            if ('' === trim($line)) {
                return null;
            }

            $line = ltrim($line, "#*/ \t");
            $line = rtrim($line, "#*/ \t");

            return trim($line);
        }, explode("\n", $value));

        // Remove empty lines.
        $lines = array_filter($lines);
        $value = implode(' ', $lines);

        return new static($value, $line, $lastLine);
    }

    /**
     * Return the line where the comment starts.
     *
     * @return int Line number.
     */
    public function getFirstLine()
    {
        return $this->firstLine;
    }

    /**
     * Return the line where the comment ends.
     *
     * @return int Line number.
     */
    public function getLastLine()
    {
        return $this->lastLine;
    }

    /**
     * Return the actual comment string.
     *
     * @return string The comment.
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Whether this comment is related with a given function.
     *
     * @param ParsedFunction $function The function to check.
     * @return bool Whether the comment is related or not.
     */
    public function isRelatedWith(ParsedFunction $function)
    {
        return $this->getLastLine() === $function->getLine() || $this->getLastLine() === $function->getLine() - 1;
    }

    /**
     * Whether the comment matches the required prefixes.
     *
     * @param array $prefixes An array of prefixes to check.
     * @return bool Whether the comment matches the prefixes or not.
     */
    public function checkPrefixes(array $prefixes)
    {
        if ('' === $this->comment) {
            return false;
        }

        if (empty($prefixes)) {
            return true;
        }

        foreach ($prefixes as $prefix) {
            if (strpos($this->comment, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }
}
