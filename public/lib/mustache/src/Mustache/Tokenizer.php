<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2017 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache Tokenizer class.
 *
 * This class is responsible for turning raw template source into a set of Mustache tokens.
 */
class Mustache_Tokenizer
{
    // Finite state machine states
    const IN_TEXT     = 0;
    const IN_TAG_TYPE = 1;
    const IN_TAG      = 2;

    // Token types
    const T_SECTION      = '#';
    const T_INVERTED     = '^';
    const T_END_SECTION  = '/';
    const T_COMMENT      = '!';
    const T_PARTIAL      = '>';
    const T_PARENT       = '<';
    const T_DELIM_CHANGE = '=';
    const T_ESCAPED      = '_v';
    const T_UNESCAPED    = '{';
    const T_UNESCAPED_2  = '&';
    const T_TEXT         = '_t';
    const T_PRAGMA       = '%';
    const T_BLOCK_VAR    = '$';
    const T_BLOCK_ARG    = '$arg';

    // Valid token types
    private static $tagTypes = array(
        self::T_SECTION      => true,
        self::T_INVERTED     => true,
        self::T_END_SECTION  => true,
        self::T_COMMENT      => true,
        self::T_PARTIAL      => true,
        self::T_PARENT       => true,
        self::T_DELIM_CHANGE => true,
        self::T_ESCAPED      => true,
        self::T_UNESCAPED    => true,
        self::T_UNESCAPED_2  => true,
        self::T_PRAGMA       => true,
        self::T_BLOCK_VAR    => true,
    );

    // Token properties
    const TYPE    = 'type';
    const NAME    = 'name';
    const OTAG    = 'otag';
    const CTAG    = 'ctag';
    const LINE    = 'line';
    const INDEX   = 'index';
    const END     = 'end';
    const INDENT  = 'indent';
    const NODES   = 'nodes';
    const VALUE   = 'value';
    const FILTERS = 'filters';

    private $state;
    private $tagType;
    private $buffer;
    private $tokens;
    private $seenTag;
    private $line;

    private $otag;
    private $otagChar;
    private $otagLen;

    private $ctag;
    private $ctagChar;
    private $ctagLen;

    /**
     * Scan and tokenize template source.
     *
     * @throws Mustache_Exception_SyntaxException when mismatched section tags are encountered
     * @throws Mustache_Exception_InvalidArgumentException when $delimiters string is invalid
     *
     * @param string $text       Mustache template source to tokenize
     * @param string $delimiters Optionally, pass initial opening and closing delimiters (default: empty string)
     *
     * @return array Set of Mustache tokens
     */
    public function scan($text, $delimiters = '')
    {
        // Setting mbstring.func_overload makes things *really* slow.
        // Let's do everyone a favor and scan this string as ASCII instead.
        //
        // The INI directive was removed in PHP 8.0 so we don't need to check there (and can drop it
        // when we remove support for older versions of PHP).
        //
        // @codeCoverageIgnoreStart
        $encoding = null;
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            if (function_exists('mb_internal_encoding') && ini_get('mbstring.func_overload') & 2) {
                $encoding = mb_internal_encoding();
                mb_internal_encoding('ASCII');
            }
        }
        // @codeCoverageIgnoreEnd

        $this->reset();

        if (is_string($delimiters) && $delimiters = trim($delimiters)) {
            $this->setDelimiters($delimiters);
        }

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            switch ($this->state) {
                case self::IN_TEXT:
                    $char = $text[$i];
                    // Test whether it's time to change tags.
                    if ($char === $this->otagChar && substr($text, $i, $this->otagLen) === $this->otag) {
                        $i--;
                        $this->flushBuffer();
                        $this->state = self::IN_TAG_TYPE;
                    } else {
                        $this->buffer .= $char;
                        if ($char === "\n") {
                            $this->flushBuffer();
                            $this->line++;
                        }
                    }
                    break;

                case self::IN_TAG_TYPE:
                    $i += $this->otagLen - 1;
                    $char = $text[$i + 1];
                    if (isset(self::$tagTypes[$char])) {
                        $tag = $char;
                        $this->tagType = $tag;
                    } else {
                        $tag = null;
                        $this->tagType = self::T_ESCAPED;
                    }

                    if ($this->tagType === self::T_DELIM_CHANGE) {
                        $i = $this->changeDelimiters($text, $i);
                        $this->state = self::IN_TEXT;
                    } elseif ($this->tagType === self::T_PRAGMA) {
                        $i = $this->addPragma($text, $i);
                        $this->state = self::IN_TEXT;
                    } else {
                        if ($tag !== null) {
                            $i++;
                        }
                        $this->state = self::IN_TAG;
                    }
                    $this->seenTag = $i;
                    break;

                default:
                    $char = $text[$i];
                    // Test whether it's time to change tags.
                    if ($char === $this->ctagChar && substr($text, $i, $this->ctagLen) === $this->ctag) {
                        $token = array(
                            self::TYPE  => $this->tagType,
                            self::NAME  => trim($this->buffer),
                            self::OTAG  => $this->otag,
                            self::CTAG  => $this->ctag,
                            self::LINE  => $this->line,
                            self::INDEX => ($this->tagType === self::T_END_SECTION) ? $this->seenTag - $this->otagLen : $i + $this->ctagLen,
                        );

                        if ($this->tagType === self::T_UNESCAPED) {
                            // Clean up `{{{ tripleStache }}}` style tokens.
                            if ($this->ctag === '}}') {
                                if (($i + 2 < $len) && $text[$i + 2] === '}') {
                                    $i++;
                                } else {
                                    $msg = sprintf(
                                        'Mismatched tag delimiters: %s on line %d',
                                        $token[self::NAME],
                                        $token[self::LINE]
                                    );

                                    throw new Mustache_Exception_SyntaxException($msg, $token);
                                }
                            } else {
                                $lastName = $token[self::NAME];
                                if (substr($lastName, -1) === '}') {
                                    $token[self::NAME] = trim(substr($lastName, 0, -1));
                                } else {
                                    $msg = sprintf(
                                        'Mismatched tag delimiters: %s on line %d',
                                        $token[self::NAME],
                                        $token[self::LINE]
                                    );

                                    throw new Mustache_Exception_SyntaxException($msg, $token);
                                }
                            }
                        }

                        $this->buffer = '';
                        $i += $this->ctagLen - 1;
                        $this->state = self::IN_TEXT;
                        $this->tokens[] = $token;
                    } else {
                        $this->buffer .= $char;
                    }
                    break;
            }
        }

        if ($this->state !== self::IN_TEXT) {
            $this->throwUnclosedTagException();
        }

        $this->flushBuffer();

        // Restore the user's encoding...
        // @codeCoverageIgnoreStart
        if ($encoding) {
            mb_internal_encoding($encoding);
        }
        // @codeCoverageIgnoreEnd

        return $this->tokens;
    }

    /**
     * Helper function to reset tokenizer internal state.
     */
    private function reset()
    {
        $this->state    = self::IN_TEXT;
        $this->tagType  = null;
        $this->buffer   = '';
        $this->tokens   = array();
        $this->seenTag  = false;
        $this->line     = 0;

        $this->otag     = '{{';
        $this->otagChar = '{';
        $this->otagLen  = 2;

        $this->ctag     = '}}';
        $this->ctagChar = '}';
        $this->ctagLen  = 2;
    }

    /**
     * Flush the current buffer to a token.
     */
    private function flushBuffer()
    {
        if (strlen($this->buffer) > 0) {
            $this->tokens[] = array(
                self::TYPE  => self::T_TEXT,
                self::LINE  => $this->line,
                self::VALUE => $this->buffer,
            );
            $this->buffer   = '';
        }
    }

    /**
     * Change the current Mustache delimiters. Set new `otag` and `ctag` values.
     *
     * @throws Mustache_Exception_SyntaxException when delimiter string is invalid
     *
     * @param string $text  Mustache template source
     * @param int    $index Current tokenizer index
     *
     * @return int New index value
     */
    private function changeDelimiters($text, $index)
    {
        $startIndex = strpos($text, '=', $index) + 1;
        $close      = '=' . $this->ctag;
        $closeIndex = strpos($text, $close, $index);

        if ($closeIndex === false) {
            $this->throwUnclosedTagException();
        }

        $token = array(
            self::TYPE => self::T_DELIM_CHANGE,
            self::LINE => $this->line,
        );

        try {
            $this->setDelimiters(trim(substr($text, $startIndex, $closeIndex - $startIndex)));
        } catch (Mustache_Exception_InvalidArgumentException $e) {
            throw new Mustache_Exception_SyntaxException($e->getMessage(), $token);
        }

        $this->tokens[] = $token;

        return $closeIndex + strlen($close) - 1;
    }

    /**
     * Set the current Mustache `otag` and `ctag` delimiters.
     *
     * @throws Mustache_Exception_InvalidArgumentException when delimiter string is invalid
     *
     * @param string $delimiters
     */
    private function setDelimiters($delimiters)
    {
        if (!preg_match('/^\s*(\S+)\s+(\S+)\s*$/', $delimiters, $matches)) {
            throw new Mustache_Exception_InvalidArgumentException(sprintf('Invalid delimiters: %s', $delimiters));
        }

        list($_, $otag, $ctag) = $matches;

        $this->otag     = $otag;
        $this->otagChar = $otag[0];
        $this->otagLen  = strlen($otag);

        $this->ctag     = $ctag;
        $this->ctagChar = $ctag[0];
        $this->ctagLen  = strlen($ctag);
    }

    /**
     * Add pragma token.
     *
     * Pragmas are hoisted to the front of the template, so all pragma tokens
     * will appear at the front of the token list.
     *
     * @param string $text
     * @param int    $index
     *
     * @return int New index value
     */
    private function addPragma($text, $index)
    {
        $end    = strpos($text, $this->ctag, $index);
        if ($end === false) {
            $this->throwUnclosedTagException();
        }

        $pragma = trim(substr($text, $index + 2, $end - $index - 2));

        // Pragmas are hoisted to the front of the template.
        array_unshift($this->tokens, array(
            self::TYPE => self::T_PRAGMA,
            self::NAME => $pragma,
            self::LINE => 0,
        ));

        return $end + $this->ctagLen - 1;
    }

    private function throwUnclosedTagException()
    {
        $name = trim($this->buffer);
        if ($name !== '') {
            $msg = sprintf('Unclosed tag: %s on line %d', $name, $this->line);
        } else {
            $msg = sprintf('Unclosed tag on line %d', $this->line);
        }

        throw new Mustache_Exception_SyntaxException($msg, array(
            self::TYPE  => $this->tagType,
            self::NAME  => $name,
            self::OTAG  => $this->otag,
            self::CTAG  => $this->ctag,
            self::LINE  => $this->line,
            self::INDEX => $this->seenTag - $this->otagLen,
        ));
    }
}
