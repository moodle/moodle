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
 * This file contains the date criteria type
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Course completion critieria - completion on specified date
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_criteria_date extends completion_criteria {

    /* @var int Criteria type constant [COMPLETION_CRITERIA_TYPE_DATE]  */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_DATE;

    /**
     * Finds and returns a data_object instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return data_object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_DATE;
        return self::fetch_helper('course_completion_criteria', __CLASS__, $params);
    }

    /**
     * Add appropriate form elements to the critieria form
     *
     * @param MoodleQuickForm $mform Moodle forms object
     * @param stdClass $data not used
     */
    public function config_form_display(&$mform, $data = null) {
        $mform->addElement('checkbox', 'criteria_date', get_string('enable'));
        $mform->addElement('date_selector', 'criteria_date_value', get_string('completionondatevalue', 'core_completion'));
        $mform->disabledIf('criteria_date_value', 'criteria_date');

        // If instance of criteria exists
        if ($this->id) {
            $mform->setDefault('criteria_date', 1);
            $mform->setDefault('criteria_date_value', $this->timeend);
        } else {
            $mform->setDefault('criteria_date_value', time() + 3600 * 24);
        }
    }

    /**
     * Update the criteria information stored in the database
     *
     * @param stdClass $data Form data
     */
    public function update_config(&$data) {
        if (!empty($data->criteria_date)) {
            $this->course = $data->id;
            $this->timeend = $data->criteria_date_value;
            $this->insert();
        }
    }

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param completion_completion $completion The user's completion record
     * @param bool $mark Optionally set false to not save changes to database
     * @return bool
     */
    public function review($completion, $mark = true) {
        // If current time is past timeend
        if ($this->timeend && $this->timeend < time()) {
            if ($mark) {
                $completion->mark_complete();
            }

            return true;
        }
        return false;
    }

    /**
     * Return criteria title for display in reports
     *
     * @return string
     */
    public function get_title() {
        return get_string('date');
    }

    /**
     * Return a more detailed criteria title for display in reports
     *
     * @return string
     */
    public function get_title_detailed() {
        return userdate($this->timeend, '%d-%h-%y');
    }

    /**
     * Return criteria type title for display in reports
     *
     * @return string
     */
    public function get_type_title() {
        return get_string('date');
    }


    /**
     * Return criteria status text for display in reports
     *
     * @param completion_completion $completion The user's completion record
     * @return string
     */
    public function get_status($completion) {
        return $completion->is_complete() ? get_string('yes') : userdate($this->timeend, '%d-%h-%y');
    }

    /**
     * Find user's who have completed this criteria
     */
    public function cron() {
        global $DB;

        // Get all users who match meet this criteria
        $sql = '
            SELECT DISTINCT
                c.id AS course,
                cr.timeend AS timeend,
                cr.id AS criteriaid,
                ra.userid AS userid
            FROM
                {course_completion_criteria} cr
            INNER JOIN
                {course} c
             ON cr.course = c.id
            INNER JOIN
                {context} con
             ON con.instanceid = c.id
            INNER JOIN
                {role_assignments} ra
             ON ra.contextid = con.id
            LEFT JOIN
                {course_completion_crit_compl} cc
             ON cc.criteriaid = cr.id
            AND cc.userid = ra.userid
            WHERE
                cr.criteriatype = '.COMPLETION_CRITERIA_TYPE_DATE.'
            AND con.contextlevel = '.CONTEXT_COURSE.'
            AND c.enablecompletion = 1
            AND cc.id IS NULL
            AND cr.timeend < ?
        ';

        // Loop through completions, and mark as complete
        $rs = $DB->get_recordset_sql($sql, array(time()));
        foreach ($rs as $record) {
            $completion = new completion_criteria_completion((array) $record, DATA_OBJECT_FETCH_BY_KEY);
            $completion->mark_complete($record->timeend);
        }
        $rs->close();
    }

    /**
     * Return criteria progress details for display in reports
     *
     * @param completion_completion $completion The user's completion record
     * @return array An array with the following keys:
     *     type, criteria, requirement, status
     */
    public function get_details($completion) {
        $details = array();
        $details['type'] = get_string('datepassed', 'completion');
        $details['criteria'] = get_string('remainingenroleduntildate', 'completion');
        $details['requirement'] = userdate($this->timeend, '%d %B %Y');
        $details['status'] = '';

        return $details;
    }

    /**
     * Return pix_icon for display in reports.
     *
     * @param string $alt The alt text to use for the icon
     * @param array $attributes html attributes
     * @return pix_icon
     */
    public function get_icon($alt, array $attributes = null) {
        return new pix_icon('i/calendar', $alt, 'moodle', $attributes);
    }

    /**
     * Shift the date when resetting course.
     *
     * @param int $courseid the course id
     * @param int $timeshift number of seconds to shift date
     * @return boolean was the operation successful?
     */
    public static function update_date($courseid, $timeshift) {
        if ($criteria = self::fetch(array('course' => $courseid))) {
            $criteria->timeend = $criteria->timeend + $timeshift;
            $criteria->update();
        }
    }
}
