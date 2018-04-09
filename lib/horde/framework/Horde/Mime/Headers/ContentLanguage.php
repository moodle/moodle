<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents the Content-Language header value (RFC 3282).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.8.0
 *
 * @property-read array $langs  The list of languages.
 */
class Horde_Mime_Headers_ContentLanguage
extends Horde_Mime_Headers_Element_Single
implements Horde_Mime_Headers_Extension_Mime
{
    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('Content-Language', $value);
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'full_value':
        case 'value':
        case 'value_single':
            return implode(',', $this->_values);

        case 'langs':
            return $this->_values;
        }

        return parent::__get($name);
    }

    /**
     * @param mixed $value  Either a single language or an array of languages.
     */
    protected function _setValue($value)
    {
        if ($value instanceof Horde_Mime_Headers_Element) {
            $value = $value->value;
        }

        if (!is_array($value)) {
            $value = array_map('trim', explode(',', $value));
        }

        $this->_values = array();
        foreach ($value as $val) {
            $this->_values[] = Horde_String::lower(
                $this->_sanityCheck(Horde_Mime::decode($val))
            );
        }
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            'content-language'
        );
    }

}
