<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Namespace data.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.21.0
 *
 * @property-read string $base  The namespace base ($name without trailing
 *                              delimiter) (UTF-8).
 * @property string $delimiter  The namespace delimiter.
 * @property boolean $hidden  Is this a hidden namespace?
 * @property string $name  The namespace name (UTF-8).
 * @property string $translation  Returns the translated name of the namespace
 *                                (UTF-8).
 * @property integer $type  The namespace type. Either self::NS_PERSONAL,
 *                          self::NS_OTHER, or self::NS_SHARED.
 */
class Horde_Imap_Client_Data_Namespace implements Serializable
{
    /* Namespace type constants. */
    const NS_PERSONAL = 1;
    const NS_OTHER = 2;
    const NS_SHARED = 3;

    /**
     * Data object.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Strips namespace information from the given mailbox name.
     *
     * @param string $mbox  Mailbox name.
     *
     * @return string  Mailbox name with namespace prefix stripped.
     */
    public function stripNamespace($mbox)
    {
        $mbox = strval($mbox);
        $name = $this->name;

        return (strlen($name) && (strpos($mbox, $name) === 0))
            ? substr($mbox, strlen($name))
            : $mbox;
    }

    /**
     */
    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        switch ($name) {
        case 'base':
            return rtrim($this->name, $this->delimiter);

        case 'delimiter':
        case 'name':
        case 'translation':
            return '';

        case 'hidden':
            return false;

        case 'type':
            return self::NS_PERSONAL;
        }

        return null;
    }

    /**
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'delimiter':
        case 'name':
        case 'translation':
            $this->_data[$name] = strval($value);
            break;

        case 'hidden':
            $this->_data[$name] = (bool)$value;
            break;

        case 'type':
            $this->_data[$name] = intval($value);
            break;
        }
    }

    /**
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     */
    public function __toString()
    {
        return $this->name;
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
        $data = @unserialize($data);
        if (!is_array($data)) {
            throw new Exception('Cache version change.');
        }
        $this->__unserialize($data);
    }

    /**
     * @return array
     */
    public function __serialize()
    {
        return $this->_data;
    }

    public function __unserialize(array $data)
    {
        $this->_data = $data;
    }

}
