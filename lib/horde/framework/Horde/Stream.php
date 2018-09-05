<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 */

/**
 * Object that adds convenience/utility methods to interacting with PHP
 * streams.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 *
 * @property boolean $utf8_char  Parse character as UTF-8 data instead of
 *                               single byte (@since 1.4.0).
 */
class Horde_Stream implements Serializable
{
    /**
     * Stream resource.
     *
     * @var resource
     */
    public $stream;

    /**
     * Configuration parameters.
     *
     * @var array
     */
    protected $_params;

    /**
     * Parse character as UTF-8 data instead of single byte.
     *
     * @var boolean
     */
    protected $_utf8_char = false;

    /**
     * Constructor.
     *
     * @param array $opts  Configuration options.
     */
    public function __construct(array $opts = array())
    {
        $this->_params = $opts;
        $this->_init();
    }

    /**
     * Initialization method.
     */
    protected function _init()
    {
        // Sane default: read-write, 0-length stream.
        if (!$this->stream) {
            $this->stream = @fopen('php://temp', 'r+');
        }
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'utf8_char':
            return $this->_utf8_char;
        }
    }

    /**
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'utf8_char':
            $this->_utf8_char = (bool)$value;
            break;
        }
    }

    /**
     */
    public function __clone()
    {
        $data = strval($this);
        $this->stream = null;
        $this->_init();
        $this->add($data);
    }

    /**
     * String representation of object.
     *
     * @since 1.1.0
     *
     * @return string  The full stream converted to a string.
     */
    public function __toString()
    {
        $this->rewind();
        return $this->substring();
    }

    /**
     * Adds data to the stream.
     *
     * @param mixed $data     Data to add to the stream. Can be a resource,
     *                        Horde_Stream object, or a string(-ish) value.
     * @param boolean $reset  Reset stream pointer to initial position after
     *                        adding?
     */
    public function add($data, $reset = false)
    {
        if ($reset) {
            $pos = $this->pos();
        }

        if (is_resource($data)) {
            $dpos = ftell($data);
            while (!feof($data)) {
                $this->add(fread($data, 8192));
            }
            fseek($data, $dpos);
        } elseif ($data instanceof Horde_Stream) {
            $dpos = $data->pos();
            while (!$data->eof()) {
                $this->add($data->substring(0, 65536));
            }
            $data->seek($dpos, false);
        } else {
            fwrite($this->stream, $data);
        }

        if ($reset) {
            $this->seek($pos, false);
        }
    }

    /**
     * Returns the length of the data. Does not change the stream position.
     *
     * @param boolean $utf8  If true, determines the UTF-8 length of the
     *                       stream (as of 1.4.0). If false, determines the
     *                       byte length of the stream.
     *
     * @return integer  Stream size.
     *
     * @throws Horde_Stream_Exception
     */
    public function length($utf8 = false)
    {
        $pos = $this->pos();

        if ($utf8 && $this->_utf8_char) {
            $this->rewind();
            $len = 0;
            while ($this->getChar() !== false) {
                ++$len;
            }
        } elseif (!$this->end()) {
            throw new Horde_Stream_Exception('ERROR');
        } else {
            $len = $this->pos();
        }

        if (!$this->seek($pos, false)) {
            throw new Horde_Stream_Exception('ERROR');
        }

        return $len;
    }

    /**
     * Get a string up to a certain character (or EOF).
     *
     * @param string $end   The character to stop reading at. As of 1.4.0,
     *                      $char can be a multi-character UTF-8 string.
     * @param boolean $all  If true, strips all repetitions of $end from
     *                      the end. If false, stops at the first instance
     *                      of $end. (@since 1.5.0)
     *
     * @return string  The string up to $end (stream is positioned after the
     *                 end character(s), all of which are stripped from the
     *                 return data).
     */
    public function getToChar($end, $all = true)
    {
        if (($len = strlen($end)) === 1) {
            $out = '';
            do {
                if (($tmp = stream_get_line($this->stream, 8192, $end)) === false) {
                    return $out;
                }

                $out .= $tmp;
                if ((strlen($tmp) < 8192) || ($this->peek(-1) == $end)) {
                    break;
                }
            } while (true);
        } else {
            $res = $this->search($end);

            if (is_null($res)) {
                return $this->substring();
            }

            $out = substr($this->getString(null, $res + $len - 1), 0, $len * -1);
        }

        /* Remove all further characters also. */
        if ($all) {
            while ($this->peek($len) == $end) {
                $this->seek($len);
            }
        }

        return $out;
    }

    /**
     * Return the current character(s) without moving the pointer.
     *
     * @param integer $length  The peek length (since 1.4.0).
     *
     * @return string  The current character.
     */
    public function peek($length = 1)
    {
        $out = '';

        for ($i = 0; $i < $length; ++$i) {
            if (($c = $this->getChar()) === false) {
                break;
            }
            $out .= $c;
        }

        $this->seek(strlen($out) * -1);

        return $out;
    }

    /**
     * Search for character(s) and return its position.
     *
     * @param string $char      The character to search for. As of 1.4.0,
     *                          $char can be a multi-character UTF-8 string.
     * @param boolean $reverse  Do a reverse search?
     * @param boolean $reset    Reset the pointer to the original position?
     *
     * @return mixed  The start position of the search string (integer), or
     *                null if character not found.
     */
    public function search($char, $reverse = false, $reset = true)
    {
        $found_pos = null;

        if ($len = strlen($char)) {
            $pos = $this->pos();
            $single_char = ($len === 1);

            do {
                if ($reverse) {
                    for ($i = $pos - 1; $i >= 0; --$i) {
                        $this->seek($i, false);
                        $c = $this->peek();
                        if ($c == ($single_char ? $char : substr($char, 0, strlen($c)))) {
                            $found_pos = $i;
                            break;
                        }
                    }
                } else {
                    /* Optimization for the common use case of searching for
                     * a single character in byte data. Reduces calling
                     * getChar() a bunch of times. */
                    $fgetc = ($single_char && !$this->_utf8_char);

                    while (($c = ($fgetc ? fgetc($this->stream) : $this->getChar())) !== false) {
                        if ($c == ($single_char ? $char : substr($char, 0, strlen($c)))) {
                            $found_pos = $this->pos() - ($single_char ? 1 : strlen($c));
                            break;
                        }
                    }
                }

                if ($single_char ||
                    is_null($found_pos) ||
                    ($this->getString($found_pos, $found_pos + $len - 1) == $char)) {
                    break;
                }

                $this->seek($found_pos + ($reverse ? 0 : 1), false);
                $found_pos = null;
            } while (true);

            $this->seek(
                ($reset || is_null($found_pos)) ? $pos : $found_pos,
                false
            );
        }

        return $found_pos;
    }

    /**
     * Returns the stream (or a portion of it) as a string. Position values
     * are the byte position in the stream.
     *
     * @param integer $start  The starting position. If positive, start from
     *                        this position. If negative, starts this length
     *                        back from the current position. If null, starts
     *                        from the current position.
     * @param integer $end    The ending position relative to the beginning of
     *                        the stream (if positive). If negative, end this
     *                        length back from the end of the stream. If null,
     *                        reads to the end of the stream.
     *
     * @return string  A string.
     */
    public function getString($start = null, $end = null)
    {
        if (!is_null($start) && ($start >= 0)) {
            $this->seek($start, false);
            $start = 0;
        }

        if (is_null($end)) {
            $len = null;
        } else {
            $end = ($end >= 0)
                ? $end - $this->pos() + 1
                : $this->length() - $this->pos() + $end;
            $len = max($end, 0);
        }

        return $this->substring($start, $len);
    }

    /**
     * Return part of the stream as a string.
     *
     * @since 1.4.0
     *
     * @param integer $start   Start, as an offset from the current postion.
     * @param integer $length  Length of string to return. If null, returns
     *                         rest of the stream. If negative, this many
     *                         characters will be omitted from the end of the
     *                         stream.
     * @param boolean $char    If true, $start/$length is the length in
     *                         characters. If false, $start/$length is the
     *                         length in bytes.
     *
     * @return string  The substring.
     */
    public function substring($start = 0, $length = null, $char = false)
    {
        if ($start !== 0) {
            $this->seek($start, true, $char);
        }

        $out = '';
        $to_end = is_null($length);

        /* If length is greater than remaining stream, use more efficient
         * algorithm below. Also, if doing a negative length, deal with that
         * below also. */
        if ($char &&
            $this->_utf8_char &&
            !$to_end &&
            ($length >= 0) &&
            ($length < ($this->length() - $this->pos()))) {
            while ($length-- && (($char = $this->getChar()) !== false)) {
                $out .= $char;
            }
            return $out;
        }

        if (!$to_end && ($length < 0)) {
            $pos = $this->pos();
            $this->end();
            $this->seek($length, true, $char);
            $length = $this->pos() - $pos;
            $this->seek($pos, false);
            if ($length < 0) {
                return '';
            }
        }

        while (!feof($this->stream) && ($to_end || $length)) {
            $read = fread($this->stream, $to_end ? 16384 : $length);
            $out .= $read;
            if (!$to_end) {
                $length -= strlen($read);
            }
        }

        return $out;
    }

    /**
     * Auto-determine the EOL string.
     *
     * @since 1.3.0
     *
     * @return string  The EOL string, or null if no EOL found.
     */
    public function getEOL()
    {
        $pos = $this->pos();

        $this->rewind();
        $pos2 = $this->search("\n", false, false);
        if ($pos2) {
            $this->seek(-1);
            $eol = ($this->getChar() == "\r")
                ? "\r\n"
                : "\n";
        } else {
            $eol = is_null($pos2)
                ? null
                : "\n";
        }

        $this->seek($pos, false);

        return $eol;
    }

    /**
     * Return a character from the string.
     *
     * @since 1.4.0
     *
     * @return string  Character (single byte, or UTF-8 character if
     *                 $utf8_char is true).
     */
    public function getChar()
    {
        $char = fgetc($this->stream);
        if (!$this->_utf8_char) {
            return $char;
        }

        $c = ord($char);
        if ($c < 0x80) {
            return $char;
        }

        if ($c < 0xe0) {
            $n = 1;
        } elseif ($c < 0xf0) {
            $n = 2;
        } elseif ($c < 0xf8) {
            $n = 3;
        } else {
            throw new Horde_Stream_Exception('ERROR');
        }

        for ($i = 0; $i < $n; ++$i) {
            if (($c = fgetc($this->stream)) === false) {
                throw new Horde_Stream_Exception('ERROR');
            }
            $char .= $c;
        }

        return $char;
    }

    /**
     * Return the current stream pointer position.
     *
     * @since 1.4.0
     *
     * @return mixed  The current position (integer), or false.
     */
    public function pos()
    {
        return ftell($this->stream);
    }

    /**
     * Rewind the internal stream to the beginning.
     *
     * @since 1.4.0
     *
     * @return boolean  True if successful.
     */
    public function rewind()
    {
        return rewind($this->stream);
    }

    /**
     * Move internal pointer.
     *
     * @since 1.4.0
     *
     * @param integer $offset  The offset.
     * @param boolean $curr    If true, offset is from current position. If
     *                         false, offset is from beginning of stream.
     * @param boolean $char    If true, $offset is the length in characters.
     *                         If false, $offset is the length in bytes.
     *
     * @return boolean  True if successful.
     */
    public function seek($offset = 0, $curr = true, $char = false)
    {
        if (!$offset) {
            return (bool)$curr ?: $this->rewind();
        }

        if ($offset < 0) {
            if (!$curr) {
                return true;
            } elseif (abs($offset) > $this->pos()) {
                return $this->rewind();
            }
        }

        if ($char && $this->_utf8_char) {
            if ($offset > 0) {
                if (!$curr) {
                    $this->rewind();
                }

                do {
                    $this->getChar();
                } while (--$offset);
            } else {
                $pos = $this->pos();
                $offset = abs($offset);

                while ($pos-- && $offset) {
                    fseek($this->stream, -1, SEEK_CUR);
                    if ((ord($this->peek()) & 0xC0) != 0x80) {
                        --$offset;
                    }
                }
            }

            return true;
        }

        return (fseek($this->stream, $offset, $curr ? SEEK_CUR : SEEK_SET) === 0);
    }

    /**
     * Move internal pointer to the end of the stream.
     *
     * @since 1.4.0
     *
     * @param integer $offset  Move this offset from the end.
     *
     * @return boolean  True if successful.
     */
    public function end($offset = 0)
    {
        return (fseek($this->stream, $offset, SEEK_END) === 0);
    }

    /**
     * Has the end of the stream been reached?
     *
     * @since 1.4.0
     *
     * @return boolean  True if the end of the stream has been reached.
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * Close the stream.
     *
     * @since 1.4.0
     */
    public function close()
    {
        if ($this->stream) {
            fclose($this->stream);
        }
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        $this->_params['_pos'] = $this->pos();

        return json_encode(array(
            strval($this),
            $this->_params
        ));
    }

    /**
     */
    public function unserialize($data)
    {
        $this->_init();

        $data = json_decode($data, true);
        $this->add($data[0]);
        $this->seek($data[1]['_pos'], false);
        unset($data[1]['_pos']);
        $this->_params = $data[1];
    }

}
