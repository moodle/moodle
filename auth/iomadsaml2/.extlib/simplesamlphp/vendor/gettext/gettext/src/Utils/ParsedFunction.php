<?php

namespace Gettext\Utils;

/**
 * Function parsed by PhpFunctionsScanner.
 */
class ParsedFunction
{
    /**
     * The function name.
     *
     * @var string
     */
    protected $name;

    /**
     * The line where the function starts.
     *
     * @var int
     */
    protected $line;

    /**
     * The strings extracted from the function arguments.
     *
     * @var string[]
     */
    protected $arguments;

    /**
     * The current index of the function (-1 if no arguments).
     *
     * @var int|null
     */
    protected $argumentIndex;

    /**
     * Shall we stop adding string chunks to the current argument?
     *
     * @var bool
     */
    protected $argumentStopped;

    /**
     * Extracted comments.
     *
     * @var ParsedComment[]|null
     */
    protected $comments;

    /**
     * Initializes the instance.
     *
     * @param string $name The function name.
     * @param int    $line The line where the function starts.
     */
    public function __construct($name, $line)
    {
        $this->name = $name;
        $this->line = $line;
        $this->arguments = [];
        $this->argumentIndex = -1;
        $this->argumentStopped = false;
        $this->comments = null;
    }

    /**
     * Stop extracting strings from the current argument (because we found something that's not a string).
     */
    public function stopArgument()
    {
        if ($this->argumentIndex === -1) {
            $this->argumentIndex = 0;
        }
        $this->argumentStopped = true;
    }

    /**
     * Go to the next argument because we a comma was found.
     */
    public function nextArgument()
    {
        if ($this->argumentIndex === -1) {
            // This should neve occur, but let's stay safe - During test/development an Exception should be thrown.
            $this->argumentIndex = 1;
        } else {
            ++$this->argumentIndex;
        }
        $this->argumentStopped = false;
    }

    /**
     * Add a string to the current argument.
     *
     * @param string|null $chunk
     */
    public function addArgumentChunk($chunk)
    {
        if ($this->argumentStopped === false) {
            if ($this->argumentIndex === -1) {
                $this->argumentIndex = 0;
            }
            if (isset($this->arguments[$this->argumentIndex])) {
                $this->arguments[$this->argumentIndex] .= $chunk;
            } else {
                $this->arguments[$this->argumentIndex] = $chunk;
            }
        }
    }

    /**
     * Add a comment associated to this function.
     *
     * @param ParsedComment $comment
     */
    public function addComment($comment)
    {
        if ($this->comments === null) {
            $this->comments = [];
        }
        $this->comments[] = $comment;
    }

    /**
     * Return the line the function starts.
     *
     * @return int Line number.
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * A closing parenthesis was found: return the final data.
     * The array returned has the following values:
     *  0 => string The function name.
     *  1 => int The line where the function starts.
     *  2 => string[] the strings extracted from the function arguments.
     *  3 => string[] the comments associated to the function.
     *
     * @return array
     */
    public function close()
    {
        $arguments = [];
        for ($i = 0; $i <= $this->argumentIndex; ++$i) {
            $arguments[$i] = isset($this->arguments[$i]) ? $this->arguments[$i] : null;
        }

        return [
            $this->name,
            $this->line,
            $arguments,
            $this->comments,
        ];
    }
}
