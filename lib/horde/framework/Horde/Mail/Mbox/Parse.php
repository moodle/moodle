<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * This object allows easy access to parsing mbox data (RFC 4155).
 *
 * See:
 * http://homepage.ntlworld.com/jonathan.deboynepollard/FGA/mail-mbox-formats
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 * @since     2.5.0
 */
class Horde_Mail_Mbox_Parse
implements ArrayAccess, Countable, Iterator
{
    /**
     * Data stream.
     *
     * @var resource
     */
    protected $_data;

    /**
     * Parsed data. Each entry is an array containing 3 keys:
     *   - date: (mixed) Date information, in DateTime object. Null if date
     *           cannot be parsed. False if message is not MBOX data.
     *   - start: (integer) Start boundary.
     *
     * @var array
     */
    protected $_parsed = array();

    /**
     * Constructor.
     *
     * @param mixed $data     The mbox data. Either a resource or a filename
     *                        as interpreted by fopen() (string).
     * @param integer $limit  Limit to this many messages; additional messages
     *                        will throw an exception.
     *
     * @throws Horde_Mail_Parse_Exception
     */
    public function __construct($data, $limit = null)
    {
        $this->_data = is_resource($data)
            ? $data
            : @fopen($data, 'r');

        if ($this->_data === false) {
            throw new Horde_Mail_Exception(
                Horde_Mail_Translation::t("Could not parse mailbox data.")
            );
        }

        rewind($this->_data);

        $i = 0;
        $last_line = null;
        /* Is this a MBOX format file? */
        $mbox = false;

        while (!feof($this->_data)) {
            if (is_null($last_line)) {
                $start = ftell($this->_data);
            }

            $line = fgets($this->_data);

            if (is_null($last_line)) {
                ltrim($line);
            }

            if (substr($line, 0, 5) == 'From ') {
                if (is_null($last_line)) {
                    /* This file is in MBOX format. */
                    $mbox = true;
                } elseif (!$mbox || (trim($last_line) !== '')) {
                    continue;
                }

                if ($limit && ($i++ > $limit)) {
                    throw new Horde_Mail_Exception(
                        sprintf(
                            Horde_Mail_Translation::t("Imported mailbox contains more than enforced limit of %u messages."),
                            $limit
                        )
                    );
                }

                $from_line = explode(' ', $line, 3);
                try {
                    $date = new DateTime($from_line[2]);
                } catch (Exception $e) {
                    $date = null;
                }

                $this->_parsed[] = array(
                    'date' => $date,
                    'start' => ftell($this->_data)
                );
            }

            /* Strip all empty lines before first data. */
            if (!is_null($last_line) || (trim($line) !== '')) {
                $last_line = $line;
            }
        }

        /* This was a single message, not a MBOX file. */
        if (empty($this->_parsed)) {
            $this->_parsed[] = array(
                'date' => false,
                'start' => $start
            );
        }
    }

    /* ArrayAccess methods. */

    /**
     */
    public function offsetExists($offset)
    {
        return isset($this->_parsed[$offset]);
    }

    /**
     */
    public function offsetGet($offset)
    {
        if (!isset($this->_parsed[$offset])) {
            return null;
        }

        $p = $this->_parsed[$offset];
        $end = isset($this->_parsed[$offset + 1])
            ? $this->_parsed[$offset + 1]['start']
            : null;
        $fd = fopen('php://temp', 'w+');

        fseek($this->_data, $p['start']);
        while (!feof($this->_data)) {
            $line = fgets($this->_data);
            if ($end && (ftell($this->_data) >= $end)) {
                break;
            }

            fwrite(
                $fd,
                (($p['date'] !== false) && substr($line, 0, 6) == '>From ')
                    ? substr($line, 1)
                    : $line
            );
        }

        $out = array(
            'data' => $fd,
            'date' => ($p['date'] === false) ? null : $p['date'],
            'size' => intval(ftell($fd))
        );
        rewind($fd);

        return $out;
    }

    /**
     */
    public function offsetSet($offset, $value)
    {
        // NOOP
    }

    /**
     */
    public function offsetUnset($offset)
    {
        // NOOP
    }

    /* Countable methods. */

    /**
     * Index count.
     *
     * @return integer  The number of messages.
     */
    public function count()
    {
        return count($this->_parsed);
    }

    /* Magic methods. */

    /**
     * String representation of the object.
     *
     * @return string  String representation.
     */
    public function __toString()
    {
        rewind($this->_data);
        return stream_get_contents($this->_data);
    }

    /* Iterator methods. */

    public function current()
    {
        $key = $this->key();

        return is_null($key)
            ? null
            : $this[$key];
    }

    public function key()
    {
        return key($this->_parsed);
    }

    public function next()
    {
        if ($this->valid()) {
            next($this->_parsed);
        }
    }

    public function rewind()
    {
        reset($this->_parsed);
    }

    public function valid()
    {
        return !is_null($this->key());
    }

}
