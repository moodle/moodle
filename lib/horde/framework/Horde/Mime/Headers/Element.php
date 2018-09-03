<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents a single header element.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 *
 * @property-read string $name  Header name.
 * @property-read string $value_single  The first header value.
 */
abstract class Horde_Mime_Headers_Element
implements IteratorAggregate
{
    /**
     * Header name (UTF-8, although limited to US-ASCII subset by RFCs).
     *
     * @var string
     */
    protected $_name;

    /**
     * Header values.
     *
     * @var array
     */
    protected $_values = array();

    /**
     * Constructor.
     *
     * @param string $name  Header name.
     * @param mixed $value  Header value(s).
     */
    public function __construct($name, $value)
    {
        $this->_name = trim($name);
        if (strpos($this->_name, ' ') !== false) {
            throw new InvalidArgumentException('Invalid header name');
        }
        $this->setValue($value);
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'name':
            return $this->_name;

        case 'value_single':
            return reset($this->_values);
        }
    }

    /**
     * Set the value of the header.
     *
     * @param mixed $value  Header value(s).
     */
    final public function setValue($value)
    {
        $this->_setValue($value);
    }

    /**
     * TODO
     */
    abstract protected function _setValue($value);

    /**
     * Returns the encoded string value(s) needed when sending the header text
     * to a RFC compliant mail submission server.
     *
     * @param array $opts  Additional options:
     *   - charset: (string) Charset to encode to.
     *              DEFAULT: UTF-8
     *
     * @return array  An array of string values.
     */
    final public function sendEncode(array $opts = array())
    {
        return $this->_sendEncode(array_merge(array(
            'charset' => 'UTF-8'
        ), $opts));
    }

    /**
     * TODO
     */
    protected function _sendEncode($opts)
    {
        return $this->_values;
    }

    /**
     * Perform sanity checking on a header value.
     *
     * @param string $data  The header data.
     *
     * @return string  The cleaned header data.
     */
    protected function _sanityCheck($data)
    {
        $charset_test = array(
            'windows-1252',
            Horde_Mime_Headers::$defaultCharset
        );

        if (!Horde_String::validUtf8($data)) {
            /* Appears to be a PHP error with the internal String structure
             * which prevents accurate manipulation of the string. Copying
             * the data to a new variable fixes things. */
            $data = substr($data, 0);

            /* Assumption: broken charset in headers is generally either
             * UTF-8 or ISO-8859-1/Windows-1252. Test these charsets
             * first before using default charset. This may be a
             * Western-centric approach, but it's better than nothing. */
            foreach ($charset_test as $charset) {
                $tmp = Horde_String::convertCharset($data, $charset, 'UTF-8');
                if (Horde_String::validUtf8($tmp)) {
                    return $tmp;
                }
            }
        }

        /* Ensure no null characters exist in header data. */
        return str_replace("\0", '', $data);
    }

    /**
     * If true, indicates the contents of the header is the default value.
     *
     * @since 2.8.0
     *
     * @return boolean  True if this header is the default value.
     */
    public function isDefault()
    {
        return false;
    }

    /* Static methods */

    /**
     * Return list of explicit header names handled by this driver.
     *
     * @return array  Header list.
     */
    public static function getHandles()
    {
        return array();
    }

    /* IteratorAggregate method */

    /**
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_values);
    }

}
