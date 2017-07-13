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
 * Abstract class representing the required implementation for classes able to process structure classes
 */
abstract class base_processor {

    abstract function pre_process_nested_element(base_nested_element $nested);
    abstract function process_nested_element(base_nested_element $nested);
    abstract function post_process_nested_element(base_nested_element $nested);

    abstract function process_final_element(base_final_element $final);

    abstract function process_attribute(base_attribute $attribute);
}

/**
 * base_processor abstract exception class
 *
 * This exceptions will be used by all the processor classes
 * in order to detect any problem or miss-configuration
 */
abstract class base_processor_exception extends moodle_exception {

    /**
     * Constructor - instantiates one base_processor_exception.
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
