<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace theme_adaptable\traits;

defined('MOODLE_INTERNAL') || die();

/**
 * Facilitates the null object pattern - https://www.wikiwand.com/en/Null_Object_pattern.
 * @package   theme_adaptable
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait null_object {

    protected $_defaults = [];

    /**
     * Has this class been set?
     * @param bool $ignoreinitialstate - if true, will consider an object with default values set by set_default as
     * not set.
     * @return bool
     */
    public function is_set($ignoreinitialstate = false) {
        $reflect = new \ReflectionClass($this);
        $props   = $reflect->getDefaultProperties();
        foreach ($props as $prop => $default) {
            if ($prop === '_defaults') {
                continue;
            }
            if (isset($this->$prop) && $this->$prop != $default) {
                if ($ignoreinitialstate) {
                    if (!isset($this->_defaults[$prop]) || $this->_defaults[$prop] !== $this->$prop) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Set and track default value
     * @param $prop
     * @param $val
     */
    protected function set_default($prop, $val) {

        if (isset($this->_defaults[$prop])) {
            throw new \coding_exception('Default value already set for '.$prop.' - '.$this->_defaults[$prop]);
        }
        $this->$prop = $val;
        $this->_defaults[$prop] = $this->$prop;
    }
}
