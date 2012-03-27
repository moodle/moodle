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
 * Course completion critieria - completion on achieving course grade
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/querylib.php';

/**
 * Course completion critieria - completion on achieving course grade
 */
class completion_criteria_grade extends completion_criteria {

    /**
     * Criteria type constant
     * @var int
     */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_GRADE;

    /**
     * Finds and returns a data_object instance based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_GRADE;
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
        $mform->addElement('checkbox', 'criteria_grade', get_string('enable'));
        $mform->addElement('text', 'criteria_grade_value', get_string('passinggrade', 'completion'));
        $mform->setDefault('criteria_grade_value', $data);

        if ($this->id) {
            $mform->setDefault('criteria_grade', 1);
            $mform->setDefault('criteria_grade_value', $this->gradepass);
        }
    }

    /**
     * Update the criteria information stored in the database
     * @access  public
     * @param   array   $data   Form data
     * @return  void
     */
    public function update_config(&$data) {

        // TODO validation
        if (!empty($data->criteria_grade) && is_numeric($data->criteria_grade_value))
        {
            $this->course = $data->id;
            $this->gradepass = $data->criteria_grade_value;
            $this->insert();
        }
    }

    /**
     * Get user's course grade in this course
     * @static
     * @access  private
     * @param   object  $completion
     * @return  float
     */
    private function get_grade($completion) {
        $grade = grade_get_course_grade($completion->userid, $this->course);
        return $grade->grade;
    }

    /**
     * Review this criteria and decide if the user has completed
     * @access  public
     * @param   object  $completion     The user's completion record
     * @param   boolean $mark           Optionally set false to not save changes to database
     * @return  boolean
     */
    public function review($completion, $mark = true) {
        // Get user's course grade
        $grade = $this->get_grade($completion);

        // If user's current course grade is higher than the required pass grade
        if ($this->gradepass && $this->gradepass <= $grade) {
            if ($mark) {
                $completion->gradefinal = $grade;
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
        return get_string('grade');
    }

    /**
     * Return a more detailed criteria title for display in reports
     * @access  public
     * @return  string
     */
    public function get_title_detailed() {
        return (float) $this->gradepass . '% required';
    }

    /**
     * Return criteria type title for display in reports
     * @access  public
     * @return  string
     */
    public function get_type_title() {
        return get_string('grade');
    }

    /**
     * Return criteria status text for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  string
     */
    public function get_status($completion) {
        // Cast as floats to get rid of excess decimal places
        $grade = (float) $this->get_grade($completion);
        $gradepass = (float) $this->gradepass;

        if ($grade) {
            return $grade.'% ('.$gradepass.'% to pass)';
        } else {
            return $gradepass.'% to pass';
        }
    }

    /**
     * Find user's who have completed this criteria
     * @access  public
     * @return  void
     */
    public function cron() {
        global $DB;

        // Get all users who meet this criteria
        $sql = '
            SELECT DISTINCT
                c.id AS course,
                cr.id AS criteriaid,
                ra.userid AS userid,
                gg.finalgrade AS gradefinal,
                gg.timemodified AS timecompleted
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
            INNER JOIN
                {grade_items} gi
             ON gi.courseid = c.id
            AND gi.itemtype = \'course\'
            INNER JOIN
                {grade_grades} gg
             ON gg.itemid = gi.id
            AND gg.userid = ra.userid
            LEFT JOIN
                {course_completion_crit_compl} cc
             ON cc.criteriaid = cr.id
            AND cc.userid = ra.userid
            WHERE
                cr.criteriatype = '.COMPLETION_CRITERIA_TYPE_GRADE.'
            AND con.contextlevel = '.CONTEXT_COURSE.'
            AND c.enablecompletion = 1
            AND cc.id IS NULL
            AND gg.finalgrade >= cr.gradepass
        ';

        // Loop through completions, and mark as complete
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $record) {
            $completion = new completion_criteria_completion($record, DATA_OBJECT_FETCH_BY_KEY);
            $completion->mark_complete($record['timecompleted']);
        }
        $rs->close();
    }

    /**
     * Return criteria progress details for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  array
     */
    public function get_details($completion) {
        $details = array();
        $details['type'] = get_string('coursegrade', 'completion');
        $details['criteria'] = get_string('passinggrade', 'completion');
        $details['requirement'] = ((float)$this->gradepass).'%';
        $details['status'] = '';

        $grade = (float)$this->get_grade($completion);
        if ($grade) {
            $details['status'] = $grade.'%';
        }

        return $details;
    }
}
