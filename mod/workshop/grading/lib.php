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
 * This file defines interface of all grading strategy logic classes
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('WORKSHOP_STRATEGY_MINDIMS', 3);    // default number of dimensions to show
define('WORKSHOP_STRATEGY_ADDDIMS', 2);    // number of dimensions to add

/**
 * Strategy interface defines all methods that strategy subplugins has to implement
 */
interface workshop_strategy {

    /**
     * Factory method returning a form that is used to define the assessment form
     *
     * @param string $actionurl URL of the action handler script, defaults to auto detect
     * @return stdClass The instance of the assessment form editor class
     */
    public function get_edit_strategy_form($actionurl=null);

    /**
     * Saves the assessment dimensions and other grading form elements
     *
     * Assessment dimension (also know as assessment element) represents one aspect or criterion
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshop_forms_{strategyname} tables.
     *
     * @param stdClass $data Raw data as returned by the form editor
     * @return void
     */
    public function save_edit_strategy_form(stdClass $data);

    /**
     * Factory method returning an instance of an assessment form
     *
     * @param moodle_url $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode          Mode to open the form in: preview or assessment
     */
    public function get_assessment_form(moodle_url $actionurl=null, $mode='preview');

    /**
     * Saves the filled assessment and returns the grade for submission as suggested by the reviewer
     *
     * This method processes data submitted using the form returned by {@link get_assessment_form()}
     *
     * @param stdClass $assessment Assessment being filled
     * @param stdClass $data       Raw data as returned by the assessment form
     * @return float|null          Raw grade (0 to 1) for submission as suggested by the peer or null if impossible to count
     */
    public function save_assessment(stdClass $assessment, stdClass $data);
}
