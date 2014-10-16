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
 * Abstract class for a form element representing something about a grade_grade.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

defined('MOODLE_INTERNAL') || die;

/**
 * Abstract class for a form element representing something about a grade_grade.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_attribute_format extends attribute_format implements unique_name, tabbable {

    /** @var string $name The first part of the name attribute of the form input */
    public $name;

    /** @var string $label The label of the input */
    public $label;

    /** @var grade_grade $grade The grade_grade of the input */
    public $grade;

    /** @var int $tabindex The tabindex of the input */
    public $tabindex;

    /**
     * Constructor
     *
     * @param grade_grade $grade The grade_grade we are editing.
     * @param int $tabindex The tabindex for the input.
     */
    public function __construct($grade = 0, $tabindex = 1) {

        $this->grade = $grade;
        $this->tabindex = $tabindex;
    }

    /**
     * Get a unique name for this form input
     *
     * @return string The form input name attribute.
     */
    public function get_name() {
        return "{$this->name}_{$this->grade->itemid}_{$this->grade->userid}";
    }

    /**
     * Get the tabindex for this form input
     *
     * @return int The tab index
     */
    public function get_tabindex() {
        return isset($this->tabindex) ? $this->tabindex : null;
    }

    /**
     * Should be overridden by the child class to save the value returned in this input.
     *
     * @param string $value The value from the form.
     * @return string Any error message
     */
    public abstract function set($value);
}
