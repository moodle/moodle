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
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Strategy interface defines all methods that strategy subplugins has to implement
 */
interface workshop_strategy {

    /**
     * Factory method returning a form that is used to define the assessment form
     *
     * @param string $actionurl URL of the action handler script, defaults to auto detect
     * @return stdclass The instance of the assessment form editor class
     */
    public function get_edit_strategy_form($actionurl=null);

    /**
     * Saves the assessment dimensions and other grading form elements
     *
     * Assessment dimension (also know as assessment element) represents one aspect or criterion
     * to be evaluated. Each dimension consists of a set of form fields. Strategy-specific information
     * are saved in workshopform_{strategyname} tables.
     *
     * @param stdClass $data Raw data as returned by the form editor
     * @return void
     */
    public function save_edit_strategy_form(stdclass $data);

    /**
     * Factory method returning an instance of an assessment form
     *
     * @param moodle_url $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode          Mode to open the form in: preview|assessment
     * @param stdClass $assessment  If opening in the assessment mode, the current assessment record
     * @param bool $editable        Shall the form be opened as editable (true) or read-only (false)
     * @param array $options        More assessment form options, editableweight implemented only now
     */
    public function get_assessment_form(?moodle_url $actionurl=null, $mode='preview', ?stdclass $assessment=null, $editable=true, $options=array());

    /**
     * Saves the filled assessment and returns the grade for submission as suggested by the reviewer
     *
     * This method processes data submitted using the form returned by {@link get_assessment_form()}
     * The returned grade should be rounded to 5 decimals as with round($grade, 5).
     *
     * @see grade_floatval()
     * @param stdClass $assessment Assessment being filled
     * @param stdClass $data       Raw data as returned by the assessment form
     * @return float|null          Raw percentual grade (0.00000 to 100.00000) for submission
     *                             as suggested by the peer or null if impossible to count
     */
    public function save_assessment(stdclass $assessment, stdclass $data);

    /**
     * Has the assessment form been defined and is ready to be used by the reviewers?
     *
     * @return boolean
     */
    public function form_ready();

    /**
     * Returns a general information about the assessment dimensions
     *
     * @return array [dimid] => stdclass (->id ->max ->min ->weight and optionally ->scale containing scale items)
     */
    public function get_dimensions_info();

    /**
     * Returns recordset with detailed information of all assessments done using this strategy
     *
     * The returned structure must be a recordset of objects containing at least properties:
     * submissionid, assessmentid, assessmentweight, reviewerid, gradinggrade, dimensionid and grade.
     * It is possible to pass user id(s) of reviewer(s). Then, the method returns just the reviewer's
     * assessments info.
     *
     * @param array|int|null $restrict optional id or ids of the reviewer
     * @return moodle_recordset
     */
    public function get_assessments_recordset($restrict=null);

    /**
     * Is a given scale used by the instance of workshop?
     *
     * If the grading strategy does not use scales, it should just return false. If the strategy
     * supports scales, it returns true if the given scale is used.
     * If workshopid is null, it checks for any workshop instance. If workshopid is provided,
     * it checks the given instance only.
     *
     * @param int $scaleid id of the scale to check
     * @param int|null $workshopid id of workshop instance to check, checks all in case of null
     * @return bool
     */
    public static function scale_used($scaleid, $workshopid=null);

    /**
     * Delete all data related to a given workshop module instance
     *
     * This is called from {@link workshop_delete_instance()}.
     *
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid);
}
