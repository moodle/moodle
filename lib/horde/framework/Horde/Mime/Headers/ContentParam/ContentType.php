<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents a Content-Type MIME header (RFC 2045).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.8.0
 *
 * @property-read string $ptype  The primary type.
 * @property-read string $stype  The sub type.
 * @property-read string $type_charset  The MIME type with the charset
 *                                      parameter added (if this is a text/*
 *                                      part).
 */
class Horde_Mime_Headers_ContentParam_ContentType
extends Horde_Mime_Headers_ContentParam
{
    const DEFAULT_CONTENT_TYPE = 'application/octet-stream';

    /**
     * Creates a default Content-Type header, conforming to the MIME
     * specification as detailed in RFC 2045.
     *
     * @return Horde_Mime_Headers_ContentParam_ContentType  Content-Type
     *                                                      header object.
     */
    public static function create()
    {
        $ob = new stdClass;
        $ob->value = self::DEFAULT_CONTENT_TYPE;

        return new self(null, $ob);
    }

    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('Content-Type', $value);
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'params':
            $params = new Horde_Support_CaseInsensitiveArray(
                parent::__get($name)
            );
            foreach ($params as $key => $val) {
                if (!isset($this[$key])) {
                    unset($params[$key]);
                }
            }
            return $params->getArrayCopy();

        case 'ptype':
            $val = $this->value;
            return substr($val, 0, strpos($val, '/'));

        case 'stype':
            $val = $this->value;
            return substr($val, strpos($val, '/') + 1);

        case 'type_charset':
            $val = $this->value;
            foreach ($this->_escapeParams(array_filter(array('charset' => $this['charset']))) as $k2 => $v2) {
                $val .= '; ' . $k2 . '=' . $v2;
            }
            return $val;
        }

        return parent::__get($name);
    }

    /**
     */
    public function setContentParamValue($data)
    {
        /* Set the value first, since it will handle any sanity checking. */
        parent::setContentParamValue(Horde_String::lower($data));

        $val = $this->value;

        if (strpos($val, '/') === false) {
            parent::setContentParamValue(self::DEFAULT_CONTENT_TYPE);
        } else {
            switch ($this->ptype) {
            case 'multipart':
                if (!isset($this['boundary'])) {
                    $this['boundary'] = '=_' . new Horde_Support_Randomid();
                }
                break;

            case 'application':
            case 'audio':
            case 'image':
            case 'message':
            case 'model':
            case 'text':
            case 'video':
                // No-op
                break;

            default:
                if (substr($val, 0, 2) !== 'x-') {
                    /* Append 'x-' for any unknown primary MIME type. */
                    parent::setContentParamValue('x-' . $val);
                }
                break;
            }
        }
    }

    /**
     */
    public function isDefault()
    {
        return ($this->full_value === 'text/plain');
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            'content-type'
        );
    }

    /* ArrayAccess methods. */

    /**
     */
    public function offsetExists($offset)
    {
        if (!parent::offsetExists($offset)) {
            return false;
        }

        if (strcasecmp($offset, 'boundary') === 0) {
            return ($this->ptype === 'multipart');
        } elseif (strcasecmp($offset, 'charset') === 0) {
            return (($this->ptype === 'text') &&
                    (parent::offsetGet($offset) !== 'us-ascii'));
        }

        return true;
    }

    /**
     */
    public function offsetGet($offset)
    {
        return isset($this[$offset])
            ? parent::offsetGet($offset)
            : null;
    }

    /**
     */
    public function offsetSet($offset, $value)
    {
        /* Store character set as lower case value. */
        if (strcasecmp($offset, 'charset') === 0) {
            $value = Horde_String::lower($value);
        }

        parent::offsetSet($offset, $value);
    }

    /**
     */
    public function offsetUnset($offset)
    {
        if (($this->ptype !== 'multipart') ||
            (strcasecmp($offset, 'boundary') !== 0)) {
            parent::offsetUnset($offset);
        }
    }

}
