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
 * @package   Mime
 */

/**
 * Interface representing a single named header element that can appear
 * multiple times in a message part.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 *
 * @property-read array $full_value  List of full header values (strings).
 * @property-read array $value  List of header values (strings).
 */
class Horde_Mime_Headers_Element_Multiple
extends Horde_Mime_Headers_Element
{
    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'full_value':
        case 'value':
            return $this->_values;
        }

        return parent::__get($name);
    }

    /**
     */
    protected function _setValue($value)
    {
        if ($value instanceof Horde_Mime_Headers_Element) {
            $value = $value->value;
        }

        foreach ((is_array($value) ? $value : array($value)) as $val) {
            $this->_values[] = $this->_sanityCheck(Horde_Mime::decode($val));
        }
    }

}
