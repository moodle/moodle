<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 */

/**
 * Implementation of Horde_Stream that uses a PHP native string variable
 * until a certain size is reached, at which point it converts storage to a
 * PHP temporary stream.
 *
 * NOTE: Do NOT use this class if it's possible that a stream_filter will need
 * to be added to the stream by some client code. If the size of the data
 * does not exceed max_memory there will be no stream to attach to.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 * @since     1.6.0
 *
 * @property-read boolean $use_stream  If true, the object is using a PHP temp
 *                                     stream internally.
 */
class Horde_Stream_TempString extends Horde_Stream_Temp
{
    /**
     * String stream object.
     *
     * @var Horde_Stream_String
     */
    protected $_string;

    /**
     */
    public function __construct(array $opts = array())
    {
        parent::__construct($opts);

        $temp = '';
        $this->_string = new Horde_Stream_String(array(
            'string' => $temp
        ));
    }

    /**
     */
    protected function _init()
    {
        if (!isset($this->_params['max_memory'])) {
            $this->_params['max_memory'] = 2097152;
        }

        if (!$this->_string) {
            parent::_init();
        }
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'stream':
            if ($this->_string) {
                return $this->_string->stream;
            }
            break;

        case 'use_stream':
            return !(bool)$this->_string;
        }

        return parent::__get($name);
    }

    /**
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'utf8_char':
            if ($this->_string) {
                $this->_string->utf8_char = $value;
            }
            break;
        }

        parent::__set($name, $value);
    }

    /**
     */
    public function __clone()
    {
        if ($this->_string) {
            $this->_string = clone $this->_string;
        } else {
            parent::__clone();
        }
    }

    /**
     */
    public function __toString()
    {
        return $this->_string
            ? strval($this->_string)
            : parent::__toString();
    }

    /**
     */
    public function add($data, $reset = false)
    {
        if ($this->_string && is_string($data)) {
            if ((strlen($data) + $this->_string->length()) < $this->_params['max_memory']) {
                $this->_string->add($data, $reset);
                return;
            }

            parent::_init();
            parent::add(strval($this->_string));
            $this->seek($this->_string->pos(), false);
            unset($this->_string);
        }

        parent::add($data, $reset);
    }

    /**
     */
    public function length($utf8 = false)
    {
        return $this->_string
            ? $this->_string->length($utf8)
            : parent::length($utf8);
    }

    /**
     */
    public function getToChar($end, $all = true)
    {
        return $this->_string
            ? $this->_string->getToChar($end, $all)
            : parent::getToChar($end, $all);
    }


    /**
     */
    public function peek($length = 1)
    {
        return $this->_string
            ? $this->_string->peek($length)
            : parent::peek($length);
    }

    /**
     */
    public function search($char, $reverse = false, $reset = true)
    {
        return $this->_string
            ? $this->_string->search($char, $reverse, $reset)
            : parent::search($char, $reverse, $reset);
    }

    /**
     */
    public function getString($start = null, $end = null)
    {
        return $this->_string
            ? $this->_string->getString($start, $end)
            : parent::getString($start, $end);
    }

    /**
     */
    public function substring($start = 0, $length = null, $char = false)
    {
        return $this->_string
            ? $this->_string->substring($start, $length, $char)
            : parent::substring($start, $length, $char);
    }

    /**
     */
    public function getChar()
    {
        return $this->_string
            ? $this->_string->getChar()
            : parent::getChar();
    }

    /**
     */
    public function pos()
    {
        return $this->_string
            ? $this->_string->pos()
            : parent::pos();
    }

    /**
     */
    public function rewind()
    {
        return $this->_string
            ? $this->_string->rewind()
            : parent::rewind();
    }

    /**
     */
    public function seek($offset = 0, $curr = true, $char = false)
    {
        return $this->_string
            ? $this->_string->seek($offset, $curr, $char)
            : parent::seek($offset, $curr, $char);
    }

    /**
     */
    public function end($offset = 0)
    {
        return $this->_string
            ? $this->_string->end($offset)
            : parent::end($offset);
    }

    /**
     */
    public function eof()
    {
        return $this->_string
            ? $this->_string->eof()
            : parent::eof();
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    /**
     */
    public function unserialize($data)
    {
        $this->__unserialize(unserialize($data));
    }

    /**
     * @return array
     */
    public function __serialize()
    {
        if ($this->_string) {
            return array(
                $this->_string,
                $this->_params
            );
        } else {
            return parent::__serialize();
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function __unserialize($data)
    {
        if ($data[0] instanceof Horde_Stream_String) {
            $this->_string = $data[0];
            $this->_params = $data[1];
        } else {
            parent::__unserialize($data);
        }
    }

}
