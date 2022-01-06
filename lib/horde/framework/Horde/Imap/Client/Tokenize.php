<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Tokenization of an IMAP data stream.
 *
 * NOTE: This class is NOT intended to be accessed outside of this package.
 * There is NO guarantees that the API of this class will not change across
 * versions.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @internal
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @property-read boolean $eos  Has the end of the stream been reached?
 */
class Horde_Imap_Client_Tokenize implements Iterator
{
    /**
     * Current data.
     *
     * @var mixed
     */
    protected $_current = false;

    /**
     * Current key.
     *
     * @var integer
     */
    protected $_key = false;

    /**
     * Sublevel.
     *
     * @var integer
     */
    protected $_level = false;

    /**
     * Array of literal stream objects.
     *
     * @var array
     */
    protected $_literals = array();

    /**
     * Return Horde_Stream object for literal tokens?
     *
     * @var boolean
     */
    protected $_literalStream = false;

    /**
     * next() modifiers.
     *
     * @var array
     */
    protected $_nextModify = array();

    /**
     * Data stream.
     *
     * @var Horde_Stream
     */
    protected $_stream;

    /**
     * Constructor.
     *
     * @param mixed $data  Data to add (string, resource, or Horde_Stream
     *                     object).
     */
    public function __construct($data = null)
    {
        $this->_stream = new Horde_Stream_Temp();

        if (!is_null($data)) {
            $this->add($data);
        }
    }

    /**
     */
    public function __clone()
    {
        throw new LogicException('Object can not be cloned.');
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'eos':
            return $this->_stream->eof();
        }
    }

    /**
     */
    public function __sleep()
    {
        throw new LogicException('Object can not be serialized.');
    }

    /**
     */
    public function __toString()
    {
        $pos = $this->_stream->pos();
        $out = $this->_current . ' ' . $this->_stream->getString();
        $this->_stream->seek($pos, false);
        return $out;
    }

    /**
     * Add data to buffer.
     *
     * @param mixed $data  Data to add (string, resource, or Horde_Stream
     *                     object).
     */
    public function add($data)
    {
        $this->_stream->add($data);
    }

    /**
     * Add data to literal stream at the current position.
     *
     * @param mixed $data  Data to add (string, resource, or Horde_Stream
     *                     object).
     */
    public function addLiteralStream($data)
    {
        $pos = $this->_stream->pos();
        if (!isset($this->_literals[$pos])) {
            $this->_literals[$pos] = new Horde_Stream_Temp();
        }
        $this->_literals[$pos]->add($data);
    }

    /**
     * Flush the remaining entries left in the iterator.
     *
     * @param boolean $return    If true, return entries. Only returns entries
     *                           on the current level.
     * @param boolean $sublevel  Only flush items in current sublevel?
     *
     * @return array  The entries if $return is true.
     */
    public function flushIterator($return = true, $sublevel = true)
    {
        $out = array();

        if ($return) {
            $this->_nextModify = array(
                'level' => $sublevel ? $this->_level : 0,
                'out' => array()
            );
            $this->next();
            $out = $this->_nextModify['out'];
            $this->_nextModify = array();
        } elseif ($sublevel && $this->_level) {
            $this->_nextModify = array(
                'level' => $this->_level
            );
            $this->next();
            $this->_nextModify = array();
        } else {
            $this->_stream->end();
            $this->_stream->getChar();
            $this->_current = $this->_key = $this->_level = false;
        }

        return $out;
    }

    /**
     * Return literal length data located at the end of the stream.
     *
     * @return mixed  Null if no literal data found, or an array with these
     *                keys:
     *   - binary: (boolean) True if this is a literal8.
     *   - length: (integer) Length of the literal.
     */
    public function getLiteralLength()
    {
        if ($this->_stream->substring(-1, 1) === '}') {
            $literal_data = $this->_stream->getString(
                $this->_stream->search('{', true) - 1
            );
            $literal_len = substr($literal_data, 2, -1);

            if (is_numeric($literal_len)) {
                return array(
                    'binary' => ($literal_data[0] === '~'),
                    'length' => intval($literal_len)
                );
            }
        }

        return null;
    }

    /* Iterator methods. */

    /**
     */
    public function current()
    {
        return $this->_current;
    }

    /**
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * @return mixed  Either a string, boolean (true for open paren, false for
     *                close paren/EOS), Horde_Stream object, or null.
     */
    public function next()
    {
        $level = isset($this->_nextModify['level'])
            ? $this->_nextModify['level']
            : null;
        /* Directly access stream here to drastically reduce the number of
         * getChar() calls we would have to make. */
        $stream = $this->_stream->stream;

        do {
            $check_len = true;
            $in_quote = $text = $binary = false;

            while (($c = fgetc($stream)) !== false) {
                switch ($c) {
                case '\\':
                    $text .= $in_quote
                        ? fgetc($stream)
                        : $c;
                    break;

                case '"':
                    if ($in_quote) {
                        $check_len = false;
                        break 2;
                    }
                    $in_quote = true;
                    /* Set $text to non-false (could be empty string). */
                    $text = '';
                    break;

                default:
                    if ($in_quote) {
                        $text .= $c;
                        break;
                    }

                    switch ($c) {
                    case '(':
                        ++$this->_level;
                        $check_len = false;
                        $text = true;
                        break 3;

                    case ')':
                        if ($text === false) {
                            --$this->_level;
                            $check_len = $text = false;
                        } else {
                            $this->_stream->seek(-1);
                        }
                        break 3;

                    case '~':
                        // Ignore binary string identifier. PHP strings are
                        // binary-safe. But keep it if it is not used as string
                        // identifier.
                        $binary = true;
                        $text .= $c;
                        continue 3;

                    case '{':
                        if ($binary) {
                            $text = substr($text, 0, -1);
                        }
                        $literal_len = intval($this->_stream->getToChar('}'));
                        $pos = $this->_stream->pos();
                        if (isset($this->_literals[$pos])) {
                            $text = $this->_literals[$pos];
                            if (!$this->_literalStream) {
                                $text = strval($text);
                            }
                        } elseif ($this->_literalStream) {
                            $text = new Horde_Stream_Temp();
                            while (($literal_len > 0) && !feof($stream)) {
                                $part = $this->_stream->substring(
                                    0,
                                    min($literal_len, 8192)
                                );
                                $text->add($part);
                                $literal_len -= strlen($part);
                            }
                        } else {
                            $text = $this->_stream->substring(0, $literal_len);
                        }
                        $check_len = false;
                        break 3;

                    case ' ':
                        if ($text !== false) {
                            break 3;
                        }
                        break;

                    default:
                        $text .= $c;
                        break;
                    }
                    break;
                }
                $binary = false;
            }

            if ($check_len) {
                switch (strlen($text)) {
                case 0:
                    $text = false;
                    break;

                case 3:
                    if (strcasecmp($text, 'NIL') === 0) {
                        $text = null;
                    }
                    break;
                }
            }

            if (($text === false) && feof($stream)) {
                $this->_key = $this->_level = false;
                break;
            }

            ++$this->_key;

            if (is_null($level) || ($level > $this->_level)) {
                break;
            }

            if (($level === $this->_level) && !is_bool($text)) {
                $this->_nextModify['out'][] = $text;
            }
        } while (true);

        $this->_current = $text;

        return $text;
    }

    /**
     * Force return of literal data as stream, if next token.
     *
     * @see next()
     */
    public function nextStream()
    {
        $changed = $this->_literalStream;
        $this->_literalStream = true;

        $out = $this->next();

        if ($changed) {
            $this->_literalStream = false;
        }

        return $out;
    }

    /**
     */
    public function rewind()
    {
        $this->_stream->rewind();
        $this->_current = false;
        $this->_key = -1;
        $this->_level = 0;
    }

    /**
     */
    public function valid()
    {
        return ($this->_level !== false);
    }

}
