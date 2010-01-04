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
 * This file defines a class with dummy grading strategy logic
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/lib.php');  // interface definition

/**
 * Dummy grading strategy logic
 *
 * This is not a real strategy, it is used during the development only to demonstrate
 * the basic idea of the strategy plugin. Consider this as a "hello world" grading strategy.
 */
class workshop_dummy_strategy implements workshop_strategy {

    /** @var workshop the parent workshop instance */
    protected $workshop;

    /**
     * Constructor
     *
     * @param workshop $workshop The workshop instance record
     * @return void
     */
    public function __construct(workshop $workshop) {
        $this->workshop = $workshop;
    }

/// Public API

    /**
     * @param $actionurl URL of form handler, defaults to auto detect the current url
     */
    public function get_edit_strategy_form($actionurl=null) {
        global $CFG;    // needed because the included files use it
        global $PAGE;

        require_once(dirname(__FILE__) . '/edit_form.php');

        $customdata = array();
        $customdata['workshop'] = $this->workshop;
        $customdata['strategy'] = $this;
        $attributes = array('class' => 'editstrategyform');

        return new workshop_edit_dummy_strategy_form($actionurl, $customdata, 'post', '', $attributes);
    }

    /**
     * In dummy strategy, we can't really edit the assessment form. All other "real" strategies save
     * the form definition into {workshop_forms} and {workshop_forms_*} tables.
     *
     * @param stdClass $data Raw data returned by the dimension editor form
     */
    public function save_edit_strategy_form(stdClass $data) { }

    /**
     * Factory method returning an instance of an assessment form
     *
     * This dummy strategy uses the static assessment form that does not saves the filled data. 
     * All other "real" strategies load the form definition from {workshop_forms} and {workshop_forms_*} tables
     * and save the filled data into {workshop_grades} table.
     *
     * @param moodle_url $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode          Mode to open the form in: preview/assessment
     */
    public function get_assessment_form(moodle_url $actionurl=null, $mode='preview', stdClass $assessment=null) {
        global $CFG;    // needed because the included files use it
        require_once(dirname(__FILE__) . '/assessment_form.php');

        // set up the required custom data common for all strategies
        $customdata['strategy'] = $this;
        $customdata['mode']     = $mode;

        // set up strategy-specific custom data
        $attributes = array('class' => 'assessmentform dummy');

        return new workshop_dummy_assessment_form($actionurl, $customdata, 'post', '', $attributes);
    }

    /**
     * Real strategies would calculate and save the filled assessment here
     *
     * This method processes data submitted using the form returned by {@link get_assessment_form()}
     * We do not calculate nor save anything in this dummy strategy.
     *
     * @param stdClass $assessment Assessment being filled
     * @param stdClass $data       Raw data as returned by the assessment form
     * @return float|null          Percentual grade for submission as suggested by the peer
     */
    public function save_assessment(stdClass $assessment, stdClass $data) {
        global $DB;

        if ($grade >= 0) {
            return $data->grade / 100;
        } else {
            return 0;
        }
    }

}
