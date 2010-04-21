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

/**
 * @package    moodlecore
 * @subpackage backup-structure
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * TODO: Finish phpdocs
 */

/**
 * Abstract class representing one atom (name/value) piece of information
 */
abstract class base_atom {

    /** @var string name of the element (maps to XML name) */
    private $name;

    /** @var string value of the element (maps to XML content) */
    private $value;

    /** @var bool flag to indicate when one value has been set (true) or no (false) */
    private $is_set;

    /**
     * Constructor - instantiates one base_atom, specifying its basic info.
     *
     * @param string $name name of the element
     * @param string $value optional value of the element
     */
    public function __construct($name) {

        $this->validate_name($name); // Check name

        $this->name  = $name;
        $this->value = null;
        $this->is_set= false;
    }

    protected function validate_name($name) {
        // Validate various name constraints, throwing exception if needed
        if (empty($name)) {
            throw new base_atom_struct_exception('backupatomemptyname', $name);
        }
        if (preg_replace('/\s/', '', $name) != $name) {
            throw new base_atom_struct_exception('backupatomwhitespacename', $name);
        }
        if (preg_replace('/[^\x30-\x39\x41-\x5a\x5f\x61-\x7a]/', '', $name) != $name) {
            throw new base_atom_struct_exception('backupatomnotasciiname', $name);
        }
    }

/// Public API starts here

    public function get_name() {
        return $this->name;
    }

    public function get_value() {
        return $this->value;
    }

    public function set_value($value) {
        if ($this->is_set) {
            throw new base_atom_content_exception('backupatomalreadysetvalue', $value);
        }
        $this->value = $value;
        $this->is_set= true;
    }

    public function clean_value() {
        $this->value = null;
        $this->is_set= false;
    }

    public function is_set() {
        return $this->is_set;
    }

    public function to_string($showvalue = false) {
        $output = $this->name;
        if ($showvalue) {
            $value = $this->is_set ? $this->value : 'not set';
            $output .= ' => ' . $value;
        }
        return $output;
    }
}

/**
 * base_atom abstract exception class
 *
 * This exceptions will be used by all the base_atom classes
 * in order to detect any problem or miss-configuration
 */
abstract class base_atom_exception extends moodle_exception {

    /**
     * Constructor - instantiates one base_atom_exception.
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * base_atom exception to control all the errors while creating the objects
 *
 * This exception will be thrown each time the base_atom class detects some
 * inconsistency related with the creation of objects and their attributes
 * (wrong names)
 */
class base_atom_struct_exception extends base_atom_exception {

    /**
     * Constructor - instantiates one base_atom_struct_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * base_atom exception to control all the errors while setting the values
 *
 * This exception will be thrown each time the base_atom class detects some
 * inconsistency related with the creation of contents (values) of the objects
 * (bad contents, setting without cleaning...)
 */
class base_atom_content_exception extends base_atom_exception {

    /**
     * Constructor - instantiates one base_atom_content_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
