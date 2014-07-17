<?php
/**
 * Copyright 2009-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2009-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Stream_Wrapper
 */

/**
 * A stream wrapper that will combine multiple strings/streams into a single
 * stream.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2009-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Stream_Wrapper
 */
class Horde_Stream_Wrapper_Combine
{
    /**/
    const WRAPPER_NAME = 'horde-stream-wrapper-combine';

    /**
     * Context.
     *
     * @var resource
     */
    public $context;

    /**
     * Array that holds the various streams.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * The combined length of the stream.
     *
     * @var integer
     */
    protected $_length = 0;

    /**
     * The current position in the string.
     *
     * @var integer
     */
    protected $_position = 0;

    /**
     * The current position in the data array.
     *
     * @var integer
     */
    protected $_datapos = 0;

    /**
     * Have we reached EOF?
     *
     * @var boolean
     */
    protected $_ateof = false;

    /**
     * Unique ID tracker for the streams.
     *
     * @var integer
     */
    static private $_id = 0;

    /**
     * Create a stream from multiple data sources.
     *
     * @since 2.1.0
     *
     * @param array $data  An array of strings and/or streams to combine into
     *                     a single stream.
     *
     * @return resource  A PHP stream.
     */
    static public function getStream($data)
    {
        if (!self::$_id) {
            stream_wrapper_register(self::WRAPPER_NAME, __CLASS__);
        }

        return fopen(
            self::WRAPPER_NAME . '://' . ++self::$_id,
            'wb',
            false,
            stream_context_create(array(
                self::WRAPPER_NAME => array(
                    'data' => $data
                )
            ))
        );
    }
    /**
     * @see streamWrapper::stream_open()
     *
     * @param string $path
     * @param string $mode
     * @param integer $options
     * @param string &$opened_path
     *
     * @throws Exception
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $opts = stream_context_get_options($this->context);

        if (isset($opts[self::WRAPPER_NAME]['data'])) {
            $data = $opts[self::WRAPPER_NAME]['data'];
        } elseif (isset($opts['horde-combine']['data'])) {
            // @deprecated
            $data = $opts['horde-combine']['data']->getData();
        } else {
            throw new Exception('Use ' . __CLASS__ . '::getStream() to initialize the stream.');
        }

        reset($data);
        while (list(,$val) = each($data)) {
            if (is_string($val)) {
                $fp = fopen('php://temp', 'r+');
                fwrite($fp, $val);
            } else {
                $fp = $val;
            }

            fseek($fp, 0, SEEK_END);
            $length = ftell($fp);
            rewind($fp);

            $this->_data[] = array(
                'fp' => $fp,
                'l' => $length,
                'p' => 0
            );

            $this->_length += $length;
        }

        return true;
    }

    /**
     * @see streamWrapper::stream_read()
     *
     * @param integer $count
     *
     * @return mixed
     */
    public function stream_read($count)
    {
        if ($this->stream_eof()) {
            return false;
        }

        $out = '';

        while ($count) {
            $tmp = &$this->_data[$this->_datapos];
            $curr_read = min($count, $tmp['l'] - $tmp['p']);
            $out .= fread($tmp['fp'], $curr_read);
            $count -= $curr_read;
            $this->_position += $curr_read;

            if ($this->_position == $this->_length) {
                if ($count) {
                    $this->_ateof = true;
                    break;
                } else {
                    $tmp['p'] += $curr_read;
                }
            } elseif ($count) {
                $tmp = &$this->_data[++$this->_datapos];
                rewind($tmp['fp']);
                $tmp['p'] = 0;
            } else {
                $tmp['p'] += $curr_read;
            }
        }

        return $out;
    }

    /**
     * @see streamWrapper::stream_write()
     *
     * @param string $data
     *
     * @return integer
     */
    public function stream_write($data)
    {
        $tmp = &$this->_data[$this->_datapos];

        $oldlen = $tmp['l'];
        $res = fwrite($tmp['fp'], $data);
        if ($res === false) {
            return false;
        }

        $tmp['p'] = ftell($tmp['fp']);
        if ($tmp['p'] > $oldlen) {
            $tmp['l'] = $tmp['p'];
            $this->_length += ($tmp['l'] - $oldlen);
        }

        return $res;
    }

    /**
     * @see streamWrapper::stream_tell()
     *
     * @return integer
     */
    public function stream_tell()
    {
        return $this->_position;
    }

    /**
     * @see streamWrapper::stream_eof()
     *
     * @return boolean
     */
    public function stream_eof()
    {
        return $this->_ateof;
    }

    /**
     * @see streamWrapper::stream_stat()
     *
     * @return array
     */
    public function stream_stat()
    {
        return array(
            'dev' => 0,
            'ino' => 0,
            'mode' => 0,
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => $this->_length,
            'atime' => 0,
            'mtime' => 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0
        );
    }

    /**
     * @see streamWrapper::stream_seek()
     *
     * @param integer $offset
     * @param integer $whence  SEEK_SET, SEEK_CUR, or SEEK_END
     *
     * @return boolean
     */
    public function stream_seek($offset, $whence)
    {
        $oldpos = $this->_position;
        $this->_ateof = false;

        switch ($whence) {
        case SEEK_SET:
            $offset = $offset;
            break;

        case SEEK_CUR:
            $offset = $this->_position + $offset;
            break;

        case SEEK_END:
            $offset = $this->_length + $offset;
            break;

        default:
            return false;
        }

        $count = $this->_position = min($this->_length, $offset);

        foreach ($this->_data as $key => $val) {
            if ($count < $val['l']) {
                $this->_datapos = $key;
                $val['p'] = $count;
                fseek($val['fp'], $count, SEEK_SET);
                break;
            }
            $count -= $val['l'];
        }

        return ($oldpos != $this->_position);
    }

}
