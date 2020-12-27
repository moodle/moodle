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
 * This class represents a Content-Disposition MIME header (RFC 2183).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.8.0
 */
class Horde_Mime_Headers_ContentParam_ContentDisposition
extends Horde_Mime_Headers_ContentParam
{
    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('Content-Disposition', $value);
    }

    /**
     */
    public function __get($name)
    {
        $val = parent::__get($name);

        switch ($name) {
        case 'full_value':
            $val = parent::__get($name);
            if (substr(ltrim($val), 0, 1) === ';') {
                $val = 'attachment' . $val;
            }
            break;
        }

        return $val;
    }

    /**
     */
    public function setContentParamValue($data)
    {
        parent::setContentParamValue($data);

        if (strlen($val = $this->value)) {
            if (strcasecmp($val, 'attachment') === 0) {
                $val2 = 'attachment';
            } elseif (strcasecmp($val, 'inline') === 0) {
                $val2 = 'inline';
            } else {
                $val2 = '';
            }

            if ($val !== $val2) {
                parent::setContentParamValue($val2);
            }
        }
    }

    /**
     */
    public function isDefault()
    {
        return !($this->full_value);
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            'content-disposition'
        );
    }

    /* ArrayAccess methods */

    /**
     */
    public function offsetSet($offset, $value)
    {
        if (strcasecmp($offset, 'size') === 0) {
            // RFC 2183 [2.7] - size parameter
            $value = intval($this->_sanityCheck($value));
        }

        parent::offsetSet($offset, $value);
    }

}
