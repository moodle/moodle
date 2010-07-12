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
 * Class representing one path to be restored from XML file
 */
class restore_path_element {

    /** @var string name of the element */
    private $name;

    /** @var string path within the XML file this element will handle */
    private $path;

    /** @var bool flag to define if this element will get child ones grouped or no */
    private $grouped;

    /** @var object object instance in charge of processing this element. */
    private $pobject;

    /** @var mixed last data read for this element or returned data by processing method */
    private $data;

    /**
     * Constructor - instantiates one restore_path_element, specifying its basic info.
     *
     * @param string $name name of the element
     * @param string $path path of the element
     * @param bool $grouped to gather information in grouped mode or no
     */
    public function __construct($name, $path, $grouped = false) {

        $this->validate_name($name); // Check name

        $this->name = $name;
        $this->path = $path;
        $this->grouped = $grouped;
        $this->pobject = null;
        $this->data = null;
    }

    protected function validate_name($name) {
        // Validate various name constraints, throwing exception if needed
        if (empty($name)) {
            throw new restore_path_element_exception('restore_path_element_emptyname', $name);
        }
        if (preg_replace('/\s/', '', $name) != $name) {
            throw new restore_path_element_exception('restore_path_element_whitespace', $name);
        }
        if (preg_replace('/[^\x30-\x39\x41-\x5a\x5f\x61-\x7a]/', '', $name) != $name) {
            throw new restore_path_element_exception('restore_path_element_notasciiname', $name);
        }
    }

    protected function validate_pobject($pobject) {
        if (!is_object($pobject)) {
            throw new restore_path_element_exception('restore_path_element_noobject', $pobject);
        }
        if (!method_exists($pobject, $this->get_processing_method())) {
            throw new restore_path_element_exception('restore_path_element_missingmethod', $this->get_processing_method());
        }
    }


/// Public API starts here

    public function set_processing_object($pobject) {
        $this->validate_pobject($pobject);
        $this->pobject = $pobject;
    }

    public function set_data($data) {
        $this->data = $data;
    }
    public function get_name() {
        return $this->name;
    }

    public function get_path() {
        return $this->path;
    }

    public function is_grouped() {
        return $this->grouped;
    }

    public function get_processing_object() {
        return $this->pobject;
    }

    public function get_processing_method() {
        return 'process_' . $this->name;
    }

    public function get_data() {
        return $this->data;
    }
}

/**
 * restore_path_element exception class
 */
class restore_path_element_exception extends moodle_exception {

    /**
     * Constructor - instantiates one restore_path_element_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
