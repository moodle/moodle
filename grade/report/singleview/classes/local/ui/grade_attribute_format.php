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

use grade_grade;

defined('MOODLE_INTERNAL') || die;

/**
 * Abstract class for a form element representing something about a grade_grade.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_attribute_format extends attribute_format implements unique_name {

    /**
     * The first part of the name attribute of the form input
     * @var string $name
     */
    public $name;

    /**
     * The label of the input
     * @var null|string $label
     */
    public $label;

    /**
     * The grade_grade of the input
     * @var grade_grade $grade
     */
    public $grade;

    /**
     * Constructor
     *
     * @param grade_grade $grade The grade_grade we are editing.
     */
    public function __construct($grade = 0) {
        $this->grade = $grade;
    }

    /**
     * Get a unique name for this form input
     *
     * @return string The form input name attribute.
     */
    public function get_name(): string {
        return "{$this->name}_{$this->grade->itemid}_{$this->grade->userid}";
    }

    /**
     * Should be overridden by the child class to save the value returned in this input.
     *
     * @param string $value The value from the form.
     * @return string Any error message
     */
    abstract public function set($value);
}
