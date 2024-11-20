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
 * Course completion critieria - completion after specific duration from course enrolment
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Course completion critieria - completion after specific duration from course enrolment
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_criteria_duration extends completion_criteria {

    /* @var int Criteria type constant [COMPLETION_CRITERIA_TYPE_DURATION] */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_DURATION;

    /**
     * Finds and returns a data_object instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return data_object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_DURATION;
        return self::fetch_helper('course_completion_criteria', __CLASS__, $params);
    }

    /**
     * Add appropriate form elements to the critieria form
     *
     * @param moodleform $mform Moodle forms object
     * @param stdClass $data not used
     */
    public function config_form_display(&$mform, $data = null) {

        $mform->addElement('checkbox', 'criteria_duration', get_string('enable'));

        // Populate the duration length drop down.
        $thresholdmenu = array(
            // We have strings for 1 - 6 days in the core.
            86400 => get_string('secondstotime86400', 'core'),
            172800 => get_string('secondstotime172800', 'core'),
            259200 => get_string('secondstotime259200', 'core'),
            345600 => get_string('secondstotime345600', 'core'),
            432000 => get_string('secondstotime432000', 'core'),
            518400 => get_string('secondstotime518400', 'core'),
            518400 => get_string('secondstotime518400', 'core'),
        );
        // Append strings for 7 - 30 days (step by 1 day).
        for ($i = 7; $i <= 30; $i++) {
            $seconds = $i * DAYSECS;
            $thresholdmenu[$seconds] = get_string('numdays', 'core', $i);
        }
        // Append strings for 40 - 180 days (step by 10 days).
        for ($i = 40; $i <= 180; $i = $i + 10) {
            $seconds = $i * DAYSECS;
            $thresholdmenu[$seconds] = get_string('numdays', 'core', $i);
        }
        // Append string for 1 year.
        $thresholdmenu[365 * DAYSECS] = get_string('numdays', 'core', 365);

        $mform->addElement('select', 'criteria_duration_days', get_string('enrolmentdurationlength', 'core_completion'), $thresholdmenu);
        $mform->disabledIf('criteria_duration_days', 'criteria_duration');

        if ($this->id) {
            $mform->setDefault('criteria_duration', 1);
            $mform->setDefault('criteria_duration_days', $this->enrolperiod);
        }
    }

    /**
     * Update the criteria information stored in the database
     *
     * @param stdClass $data Form data
     */
    public function update_config(&$data) {
        if (!empty($data->criteria_duration)) {
            $this->course = $data->id;
            $this->enrolperiod = $data->criteria_duration_days;
            $this->insert();
        }
    }

    /**
     * Get the time this user was enroled
     *
     * @param completion_completion $completion
     * @return int
     */
    private function get_timeenrolled($completion) {
        global $DB;

        return $DB->get_field_sql('
            SELECT eu.timestart
              FROM {user_enrolments} eu
              JOIN {enrol} e ON eu.enrolid = e.id
             WHERE e.courseid = ?
               AND eu.userid = ?', array($this->course, $completion->userid));
    }

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param completion_completion $completion The user's completion record
     * @param bool $mark Optionally set false to not save changes to database
     * @return bool
     */
    public function review($completion, $mark = true) {
        $timeenrolled = $this->get_timeenrolled($completion);

        // If duration since enrollment has passed
        if (!$this->enrolperiod || !$timeenrolled) {
            return false;
        }

        if (time() > ($timeenrolled + $this->enrolperiod)) {
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
        return get_string('enrolmentduration', 'completion');
    }

    /**
     * Return a more detailed criteria title for display in reports
     *
     * @return string
     */
    public function get_title_detailed() {
        return get_string('xdays', 'completion', ceil($this->enrolperiod / (60 * 60 * 24)));
    }

    /**
     * Return criteria type title for display in reports
     *
     * @return string
     */
    public function get_type_title() {
        return get_string('days', 'completion');
    }

    /**
     * Return criteria status text for display in reports
     *
     * @param completion_completion $completion The user's completion record
     * @return string
     */
    public function get_status($completion) {
        $timeenrolled = $this->get_timeenrolled($completion);
        $timeleft = $timeenrolled + $this->enrolperiod - time();
        $enrolperiod = ceil($this->enrolperiod / (60 * 60 * 24));

        $daysleft = ceil($timeleft / (60 * 60 * 24));

        return get_string('daysoftotal', 'completion', array(
                'days' => $daysleft > 0 ? $daysleft : 0, 'total' => $enrolperiod));
    }

    /**
     * Find user's who have completed this criteria
     */
    public function cron() {
        global $DB;

        /*
         * Get all users who match meet this criteria
         *
         * We can safely ignore duplicate enrolments for
         * a user in a course here as we only care if
         * one of the enrolments has passed the set
         * duration.
         */
        $sql = '
            SELECT
                c.id AS course,
                cr.id AS criteriaid,
                u.id AS userid,
                ue.timestart AS otimestart,
                (ue.timestart + cr.enrolperiod) AS ctimestart,
                ue.timecreated AS otimeenrolled,
                (ue.timecreated + cr.enrolperiod) AS ctimeenrolled
            FROM
                {user} u
            INNER JOIN
                {user_enrolments} ue
             ON ue.userid = u.id
            INNER JOIN
                {enrol} e
             ON e.id = ue.enrolid
            INNER JOIN
                {course} c
             ON c.id = e.courseid
            INNER JOIN
                {course_completion_criteria} cr
             ON c.id = cr.course
            LEFT JOIN
                {course_completion_crit_compl} cc
             ON cc.criteriaid = cr.id
            AND cc.userid = u.id
            WHERE
                cr.criteriatype = '.COMPLETION_CRITERIA_TYPE_DURATION.'
            AND c.enablecompletion = 1
            AND cc.id IS NULL
            AND
            (
                (ue.timestart > 0 AND (ue.timestart + cr.enrolperiod) < ?)
             OR (ue.timestart = 0 AND ue.timecreated > 0 AND (ue.timecreated + cr.enrolperiod) < ?)
            )
        ';

        // Loop through completions, and mark as complete
        $now = time();
        $rs = $DB->get_recordset_sql($sql, array($now, $now));
        foreach ($rs as $record) {
            $completion = new completion_criteria_completion((array) $record, DATA_OBJECT_FETCH_BY_KEY);

            // Use time start if not 0, otherwise use timeenrolled
            if ($record->otimestart) {
                $completion->mark_complete($record->ctimestart);
            } else {
                $completion->mark_complete($record->ctimeenrolled);
            }
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
        $details['type'] = get_string('periodpostenrolment', 'completion');
        $details['criteria'] = get_string('remainingenroledfortime', 'completion');
        $details['requirement'] = get_string('xdays', 'completion', ceil($this->enrolperiod / (60*60*24)));

        // Get status
        $timeenrolled = $this->get_timeenrolled($completion);
        $timepassed = time() - $timeenrolled;
        $details['status'] = get_string('xdays', 'completion', floor($timepassed / (60*60*24)));

        return $details;
    }
}
