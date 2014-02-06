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
 * This file contains the activity completion criteria type class and any
 * supporting functions it may require.
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Course completion critieria - completion on activity completion
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_criteria_activity extends completion_criteria {

    /* @var int Criteria [COMPLETION_CRITERIA_TYPE_ACTIVITY] */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_ACTIVITY;

    /**
     * Finds and returns a data_object instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return completion_criteria_activity data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_ACTIVITY;
        return self::fetch_helper('course_completion_criteria', __CLASS__, $params);
    }

    /**
     * Add appropriate form elements to the critieria form
     *
     * @param moodleform $mform  Moodle forms object
     * @param stdClass $data details of various modules
     */
    public function config_form_display(&$mform, $data = null) {
        $mform->addElement('checkbox', 'criteria_activity['.$data->id.']', ucfirst(self::get_mod_name($data->module)).' - '.$data->name);

        if ($this->id) {
            $mform->setDefault('criteria_activity['.$data->id.']', 1);
        }
    }

    /**
     * Update the criteria information stored in the database
     *
     * @param stdClass $data Form data
     */
    public function update_config(&$data) {
        global $DB;

        if (!empty($data->criteria_activity) && is_array($data->criteria_activity)) {

            $this->course = $data->id;

            foreach (array_keys($data->criteria_activity) as $activity) {

                $module = $DB->get_record('course_modules', array('id' => $activity));
                $this->module = self::get_mod_name($module->module);
                $this->moduleinstance = $activity;
                $this->id = NULL;
                $this->insert();
            }
        }
    }

    /**
     * Get module instance module type
     *
     * @param int $type Module type id
     * @return string
     */
    public static function get_mod_name($type) {
        static $types;

        if (!is_array($types)) {
            global $DB;
            $types = $DB->get_records('modules');
        }

        return $types[$type]->name;
    }

    /**
     * Gets the module instance from the database and returns it.
     * If no module instance exists this function returns false.
     *
     * @return stdClass|bool
     */
    public function get_mod_instance() {
        global $DB;

        return $DB->get_record_sql(
            "
                SELECT
                    m.*
                FROM
                    {{$this->module}} m
                INNER JOIN
                    {course_modules} cm
                 ON cm.id = {$this->moduleinstance}
                AND m.id = cm.instance
            "
        );
    }

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param completion_completion $completion     The user's completion record
     * @param bool $mark Optionally set false to not save changes to database
     * @return bool
     */
    public function review($completion, $mark = true) {
        global $DB;

        $course = $DB->get_record('course', array('id' => $completion->course));
        $cm = $DB->get_record('course_modules', array('id' => $this->moduleinstance));
        $info = new completion_info($course);

        $data = $info->get_data($cm, false, $completion->userid);

        // If the activity is complete
        if (in_array($data->completionstate, array(COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS))) {
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
        return get_string('activitiescompleted', 'completion');
    }

    /**
     * Return a more detailed criteria title for display in reports
     *
     * @return  string
     */
    public function get_title_detailed() {
        global $DB;
        $module = $DB->get_record('course_modules', array('id' => $this->moduleinstance));
        $activity = $DB->get_record($this->module, array('id' => $module->instance));

        return shorten_text(urldecode($activity->name));
    }

    /**
     * Return criteria type title for display in reports
     *
     * @return  string
     */
    public function get_type_title() {
        return get_string('activities', 'completion');
    }

    /**
     * Find users who have completed this criteria and mark them accordingly
     */
    public function cron() {
        global $DB;

        // Get all users who meet this criteria
        $sql = '
            SELECT DISTINCT
                c.id AS course,
                cr.id AS criteriaid,
                ra.userid AS userid,
                mc.timemodified AS timecompleted
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
                {course_modules_completion} mc
             ON mc.coursemoduleid = cr.moduleinstance
            AND mc.userid = ra.userid
            LEFT JOIN
                {course_completion_crit_compl} cc
             ON cc.criteriaid = cr.id
            AND cc.userid = ra.userid
            WHERE
                cr.criteriatype = '.COMPLETION_CRITERIA_TYPE_ACTIVITY.'
            AND con.contextlevel = '.CONTEXT_COURSE.'
            AND c.enablecompletion = 1
            AND cc.id IS NULL
            AND (
                mc.completionstate = '.COMPLETION_COMPLETE.'
             OR mc.completionstate = '.COMPLETION_COMPLETE_PASS.'
                )
        ';

        // Loop through completions, and mark as complete
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $record) {
            $completion = new completion_criteria_completion((array) $record, DATA_OBJECT_FETCH_BY_KEY);
            $completion->mark_complete($record->timecompleted);
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
        // Get completion info
        $modinfo = get_fast_modinfo($completion->course);
        $cm = $modinfo->get_cm($this->moduleinstance);

        $details = array();
        $details['type'] = $this->get_title();
        if ($cm->has_view()) {
            $details['criteria'] = html_writer::link($cm->url, $cm->get_formatted_name());
        } else {
            $details['criteria'] = $cm->get_formatted_name();
        }

        // Build requirements
        $details['requirement'] = array();

        if ($cm->completion == COMPLETION_TRACKING_MANUAL) {
            $details['requirement'][] = get_string('markingyourselfcomplete', 'completion');
        } elseif ($cm->completion == COMPLETION_TRACKING_AUTOMATIC) {
            if ($cm->completionview) {
                $details['requirement'][] = get_string('viewingactivity', 'completion', $this->module);
            }

            if (!is_null($cm->completiongradeitemnumber)) {
                $details['requirement'][] = get_string('achievinggrade', 'completion');
            }
        }

        $details['requirement'] = implode($details['requirement'], ', ');

        $details['status'] = '';

        return $details;
    }
}
