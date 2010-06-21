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
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_criteria_duration extends completion_criteria {

    /**
     * Criteria type constant
     * @var int
     */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_DURATION;

    /**
     * Finds and returns a data_object instance based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_DURATION;
        return self::fetch_helper('course_completion_criteria', __CLASS__, $params);
    }

    /**
     * Add appropriate form elements to the critieria form
     * @access  public
     * @param   object  $mform  Moodle forms object
     * @param   mixed   $data   optional
     * @return  void
     */
    public function config_form_display(&$mform, $data = null) {

        $mform->addElement('checkbox', 'criteria_duration', get_string('enable'));

        $thresholdmenu=array();
        for ($i=1; $i<=30; $i++) {
            $seconds = $i * 86400;
            $thresholdmenu[$seconds] = get_string('numdays', '', $i);
        }
        $mform->addElement('select', 'criteria_duration_days', get_string('daysafterenrolment', 'completion'), $thresholdmenu);

        if ($this->id) {
            $mform->setDefault('criteria_duration', 1);
            $mform->setDefault('criteria_duration_days', $this->enrolperiod);
        }
    }

    /**
     * Update the criteria information stored in the database
     * @access  public
     * @param   array   $data   Form data
     * @return  void
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
     * @param   object  $completion
     * @return  int
     */
    private function get_timeenrolled($completion) {
        global $DB;

        $context = get_context_instance(CONTEXT_COURSE, $this->course);
        return $DB->get_field('role_assignments', 'timestart', array('contextid' => $context->id, 'userid' => $completion->userid));
    }

    /**
     * Review this criteria and decide if the user has completed
     * @access  public
     * @param   object  $completion     The user's completion record
     * @param   boolean $mark           Optionally set false to not save changes to database
     * @return  boolean
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
     * @access  public
     * @return  string
     */
    public function get_title() {
        return get_string('enrolmentduration', 'completion');
    }

    /**
     * Return a more detailed criteria title for display in reports
     * @access  public
     * @return  string
     */
    public function get_title_detailed() {
        return ceil($this->enrolperiod / (60 * 60 * 24)) . ' days';
    }

    /**
     * Return criteria type title for display in reports
     * @access  public
     * @return  string
     */
    public function get_type_title() {
        return get_string('days', 'completion');
    }

    /**
     * Return criteria status text for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  string
     */
    public function get_status($completion) {
        $timeenrolled = $this->get_timeenrolled($completion);
        $timeleft = $timeenrolled + $this->enrolperiod - time();
        $enrolperiod = ceil($this->enrolperiod / (60 * 60 * 24));

        $daysleft = ceil($timeleft / (60 * 60 * 24));

        return ($daysleft > 0 ? $daysleft : 0).' of '.$enrolperiod;
    }

    /**
     * Find user's who have completed this criteria
     * @access  public
     * @return  void
     */
    public function cron() {
        global $DB;

//TODO: MDL-22797 completion needs to be updated to use new enrolment framework

        // Get all users who match meet this criteria
        $sql = '
            SELECT DISTINCT
                c.id AS course,
                cr.timeend AS date,
                cr.id AS criteriaid,
                ra.userid AS userid,
                (ra.timestart + cr.enrolperiod) AS timecompleted
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
                cr.criteriatype = '.COMPLETION_CRITERIA_TYPE_DURATION.'
            AND con.contextlevel = '.CONTEXT_COURSE.'
            AND c.enablecompletion = 1
            AND cc.id IS NULL
            AND ra.timestart + cr.enrolperiod < ?
        ';

        // Loop through completions, and mark as complete
        if ($rs = $DB->get_recordset_sql($sql, array(time()))) {
            foreach ($rs as $record) {

                $completion = new completion_criteria_completion((array)$record);
                $completion->mark_complete($record->timecompleted);
            }

            $rs->close();
        }
    }

    /**
     * Return criteria progress details for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  array
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
