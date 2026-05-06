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

use core\exception\coding_exception;

/**
 * Used to store details of the dependency between two settings elements.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 Davo Smith, Synergy Learning
 */
namespace core_admin\setting\settingpage;

class dependency {
    /** @var string the name of the setting to be shown/hidden */
    public $settingname;
    /** @var string the setting this is dependent on */
    public $dependenton;
    /** @var string the condition to show/hide the element */
    public $condition;
    /** @var string the value to compare against */
    public $value;

    /** @var string[] list of valid conditions */
    private static $validconditions = ['checked', 'notchecked', 'noitemselected', 'eq', 'neq', 'in'];

    /**
     * Constructor for the dependency.
     *
     * @param string $settingname
     * @param string $dependenton
     * @param string $condition
     * @param string $value
     * @throws coding_exception
     */
    public function __construct($settingname, $dependenton, $condition, $value) {
        $this->settingname = $this->parse_name($settingname);
        $this->dependenton = $this->parse_name($dependenton);
        $this->condition = $condition;
        $this->value = $value;

        if (!in_array($this->condition, self::$validconditions)) {
            throw new coding_exception("Invalid condition '$condition'");
        }
    }

    /**
     * Convert the setting name into the form field name.
     * @param string $name
     * @return string
     */
    private function parse_name($name) {
        $bits = explode('/', $name);
        $name = array_pop($bits);
        $plugin = '';
        if ($bits) {
            $plugin = array_pop($bits);
            if ($plugin === 'moodle') {
                $plugin = '';
            }
        }
        return 's_'.$plugin.'_'.$name;
    }

    /**
     * Gather together all the dependencies in a format suitable for initialising javascript.
     *
     * @param self[] $dependencies
     * @return array
     */
    public static function prepare_for_javascript($dependencies) {
        $result = [];
        foreach ($dependencies as $d) {
            if (!isset($result[$d->dependenton])) {
                $result[$d->dependenton] = [];
            }
            if (!isset($result[$d->dependenton][$d->condition])) {
                $result[$d->dependenton][$d->condition] = [];
            }
            if (!isset($result[$d->dependenton][$d->condition][$d->value])) {
                $result[$d->dependenton][$d->condition][$d->value] = [];
            }
            $result[$d->dependenton][$d->condition][$d->value][] = $d->settingname;
        }
        return $result;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(dependency::class, \admin_settingdependency::class);
